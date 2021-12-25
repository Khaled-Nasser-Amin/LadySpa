<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyFatoorahController;
use App\Http\Resources\OrderResource;
use App\Mail\EmptyStockSize;
use App\Mail\OrderCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Size;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use Responses;

    public function check_promocode(Request $request)
    {
        app()->setLocale($request->lang);
        $user=$request->user();
        $normal_code=Promocode::where('code',$request->promocode)->where('type_of_code','normal')->first();
        $result=checkPromoCode($user,$user->specialCode,$normal_code,$request->promocode,'products');
        if($result){
           return response()->json($result,200);
        }else{
            return response()->json("",404);
        }

    }

    public function store(Request $request){
        app()->setLocale($request->lang);
        $user=$request->user();
        $request['payment_way']= strtolower($request['payment_way']) == 'online payment' ? 'online payment' : 'cash on delivery';

        //validation
        $validation=Validator::make($request->all(),$this->rules());
        if($validation->fails()){
            return response()->json($validation->errors(),404);
        }


        //arrang sizes ids
        $sizes_id=collect($request->sizes_id)->groupBy('id')->map(function($item){
            return [
                'id' =>$item->first()['id'],
                'quantity' => $item->sum('quantity')
            ];
        });

        //check if size or product  is active or exist
        $empty_sizes=[];
        $quantities=[];
        $validate=$this->checkIfExist($sizes_id,$empty_sizes,$quantities);
        if($validate != 'done'){
            return $validate;
        }
        $product_group_validate=$this->checkIfGroupExist($request->groups_id,$quantities);
        if($request->groups_id && $product_group_validate != 'done'){
            return $product_group_validate;
        }


        $data=$request->except(['sizes_id','lang','groups_id','promocode']);
        $order=Order::create($data);
        $user=$request->user();
        $user->orders()->save($order);
        $subtotal=0;
        $taxes=0;
        $vendors=[];
        $products=[];


        // calculate data
        $this->calcOrder($request->groups_id,$sizes_id,$subtotal,$taxes,$order,$products,$vendors);


        //associate data with order
        $this->associateDataWithOrder($user,$vendors,$request->promocode,$order,$products,$subtotal,$taxes);


        //send mail if size became empty
        $this->sendEmailToVendorsAfterEmptyStock($empty_sizes);

        if($request['payment_way'] == 'online payment'){
            $payment=new MyFatoorahController();
            $data=$payment->index($order->total_amount,$user->name,$user->phone,$user->email,$order->id);
            if($data == 'error'){
                $order->update(['payment_way' => 'cash on delivery']);
                $order->save();
                return $this->success(new OrderResource($order),__('text.Order created successfully'),305);
            }


            return $this->success(array_merge(collect($data)->toArray(),collect(new OrderResource($order))->toArray()),__('text.Order created successfully'),200);
        }
        return $this->success(new OrderResource($order),__('text.Order created successfully'),200);
    }



    //validation
    protected function rules(){
        return [
            'sizes_id' => 'required|array|min:1',
            'group_id' => 'nullable|array|min:1',
            'address' => 'required|string|max:255',
            'payment_way' => ['required',Rule::in(['cash on delivery','online payment'])],
            'lat_long' => 'required',
            'receiver_phone' => 'required|numeric',
            'receiver_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }

    //validation if exists in  database
    protected function checkIfExist($sizes_id,&$empty_sizes,&$quantities){
        foreach($sizes_id as $row){
            $size=Size::find($row['id']);
            $quantities[]=['size_id' => $size->id,'quantity' => $row['quantity']];
            if(!$size){
                return $this->error(__('text.Not Found'),404);
            }elseif($size->product->isActive == 0 || !$size->product){
                return $this->error(__('text.Product is inactive'),403);
            }elseif($size->stock <= 0){
                return $this->error(__('text.Out of Stock'),402);
            }elseif($row['quantity'] > $size->stock){
                return $this->error(__('text.Not Enough'),401);
            }
            elseif($row['quantity'] == $size->stock){
                $empty_sizes[]=$size;
            }

        }
        return 'done';
    }

    // check if the group of product availabel or not
    protected function checkIfGroupExist($groups_id,$quantities)
    {
        if(collect($groups_id)->count() > 0){
            foreach($groups_id as $row){
                $product=Product::where('type','group')->where('id',$row['id'])->first();
                foreach($product->child_products()->get() as $child){
                    foreach($child->pivot->sizes()->get() as $row){
                        $size_quantity=collect($quantities)->where('size_id',$row->id)->toArray() ;
                        $size_quantity=$size_quantity[0]['quantity'] ??  0;
                        if(($row->pivot->quantity+$size_quantity) > $row->stock){
                            return $this->error(__('text.Not Found'),404);
                        }
                    }
                }
                if(!$product  || checkCollectionActive($product)){
                    return $this->error(__('text.Not Found'),404);
                }
            }
            return 'done';
        }

    }

    // associate sizes with order and calculate stock and taxes, subtotal , total_amount
    protected function calcOrder($groups_id,$sizes_id,&$subtotal,&$taxes,&$order,&$products,&$vendors){
        foreach($sizes_id as $row){
            $size=Size::find($row['id']);
            $finalPrice=$size->sale == 0 ? $size->price : $size->sale;
            $subtotal += ( $finalPrice* $row['quantity']);
            $tax=$size->product->taxes->sum('tax') == 0 ? 0:(($finalPrice* $size->product->taxes->sum('tax'))/100)*$row['quantity'];
            $taxes  += $tax;
            $vendors[]=['vendor_id'=>$size->product->user_id,'tax' => $tax,'subtotal'=>( $finalPrice* $row['quantity'])];
            $size->order()->syncWithoutDetaching([$order->id => ['quantity' => $row['quantity'],'size' => $size->size,'price'=> $finalPrice ,'tax'=>$tax,'amount'=>$finalPrice*$row['quantity'],'total_amount'=>($finalPrice*$row['quantity'])+$tax]]);
            $size->update(['stock' => ($size->stock-$row['quantity'])]);
            $products[]=['product_id'=>$size->product_id];

        }
        if(collect($groups_id)->count() > 0){
            foreach($groups_id as $row){
            $product=Product::where('type','group')->where('id',$row['id'])->first();
            $finalPrice=$product->group_sale == 0 ? $product->group_price : $product->group_sale;
            $subtotal += ( $finalPrice* $row['quantity']);
            $tax=$product->taxes->sum('tax') == 0 ? 0:(($finalPrice* $product->taxes->sum('tax'))/100)*$row['quantity'];
            $taxes  += $tax;
            $vendors[]=['vendor_id'=>$product->user_id,'tax' => $tax,'subtotal'=>( $finalPrice* $row['quantity'])];


            $product->orders_product_group()->syncWithoutDetaching([$order->id => ['quantity' => $row['quantity'],'price'=> $finalPrice ,'tax'=>$tax,'amount' => $row['quantity']*$finalPrice,'total_amount' => ($row['quantity']*$finalPrice) + $tax]]);
            foreach($product->child_products()->get() as $child){
                foreach($child->pivot->sizes()->get() as $size){
                    $size->order_group_products_sizes()->syncWithoutDetaching([$order->id => ['product_id' => $size->product_id,'quantity' => $size->pivot->quantity,'size'=>$size->size]]);
                    $size->update(['stock' => ($size->stock-$size->pivot->quantity)]);
                }
            }

        }
        }

    }


    //associate everything with order
    protected function associateDataWithOrder($user,$vendors,$promocode,$order,$products,$subtotal,$taxes){
        $discount=$this->calculatePromoCode($promocode,$user,$subtotal);
        $this->associatePromotionCode($discount,$promocode,$user);
        $order->update(['total_amount' => ($subtotal+$taxes-$discount),'subtotal' => $subtotal,'taxes' => $taxes,'discount' => $discount]);
        $order->save();
        $this->associateProducts($products,$order);
        $this->associateVendors($vendors,$order);

    }


    //calc promo code
    protected function calculatePromoCode($promocode,$user,$subtotal)
    {
        $normal_code=Promocode::where('code',$promocode)->where('type_of_code','normal')->first();
        $result=checkPromoCode($user,$user->specialCode,$normal_code,$promocode,'products');
        if($result){
            if($result['type'] == 'percentage'){
                $discount=($result['value']*$subtotal)/100;
                if($result['constraint'] < $discount){
                    return $result['constraint'];
                }else{
                    return $discount;
                }
            }elseif($result['type'] == 'amount'){
                if($subtotal >= $result['constraint']){
                    return $result['value'];
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    //associate promotion code with user
    protected function associatePromotionCode($discount,$promocode,$user)
    {
        if($discount != 0){
            $code=Promocode::where('code',$promocode)->first();
            $code->used_customers()->syncWithoutDetaching($user->id);
            if($code->type_of_code ="special"){
                $user->specialCode()->dissociate()->save();
            }
        }

    }

    //associate vendors with order
    protected function associateVendors($vendors,$order){
        $collect=collect($vendors);
        $vendors_id=$collect->pluck('vendor_id')->unique();
        foreach($vendors_id as $id){
            $vendor=User::find($id);
            $geoLocation=explode(',',$vendor->geoLocation);
            $vendors_lat_long[]=['lat' => $geoLocation[0],'long' => $geoLocation[1]];
            $tax=$collect->where('vendor_id',$id)->sum('tax');
            $subtotal=$collect->where('vendor_id',$id)->sum('subtotal');
            $order->vendors()->syncWithoutDetaching([$id => ['taxes' =>$tax,'subtotal'=>$subtotal,'total_amount'=>$subtotal+$tax]]);
            $this->sendEmailToVendors($vendor,$order);
        }


        //calculate shipping
        $this->calculate_shipping($order,$vendors_lat_long);

        //send email to lady_spa
        if($vendors_id->search(1) == 0){
            $vendor=User::find(1);
            $this->sendEmailToVendors($vendor,$order);
        }
    }

    //associate products with order
    protected function associateProducts($products,$order){
        $collect=collect($products);
        $products_id=$collect->pluck('product_id')->unique();
        foreach($products_id as $id){
            $product=Product::find($id);
            $order->products()->syncWithoutDetaching([$product->id=>['name_ar'=>$product->name_ar,'name_en'=>$product->name_en,'image'=>$product->getAttributes()['image']]]);
        }
    }



    //send mail to all vendors in this order with order card
    protected function sendEmailToVendors($vendor,$order){
        Mail::to($vendor->email)->send(new OrderCard($order,$vendor));
    }


    //send mail to vendor if his size become out of stock
    protected function sendEmailToVendorsAfterEmptyStock($empty_sizes){

        foreach($empty_sizes as $size){
            $product_name=app()->getLocale() == 'ar' ? $size->product->name_ar: $size->product->name_en;
            $vendor_name=$size->product->user->store_name;
            $vendor_email=$size->product->user->email;
            Mail::to($vendor_email)->send(new EmptyStockSize($product_name,$vendor_name,$size->size,$size->size));
        }

    }


    //calculate shipping
    protected function calculate_shipping($order,&$vendors_lat_long)
    {
        $order_lat_long=explode(',',$order->lat_long);
        $vendors_lat_long[]=['lat'=> $order_lat_long[0],'long' => $order_lat_long[1]];
        $calc_shipping=new ShippingController();
        $shipping_cost=$calc_shipping->calc_shipping($vendors_lat_long,$order_lat_long[0],$order_lat_long[1]);
        $order->update(['shipping' => $shipping_cost,'total_amount' => $order->total_amount+$shipping_cost]);
        $order->save();
    }








    //cancel order
    public function cancel_order(Request $request){
        app()->setLocale($request->lang);
        $user=$request->user();
        $order=Order::find($request->order_id);

        if($order){
            if($user->id == $order->user_id){
                if($order->order_status == 'pending'){
                    foreach($order->sizes()->withTrashed()->get() as $size){
                        $quantity=$order->sizes->where('id',$size->id)->pluck('pivot.quantity')->first();
                        $size->update(['stock' => $size->stock+$quantity]);
                    }
                    $order->delete();
                    return $this->success('',__('text.Order cancelled successfully'),200);
                }else{
                    return $this->error(__('text.Order already shipped'),404);
                }

            }else{
                return $this->error(__('text.Oops, UNAUTHORIZED'),402);
            }
        }else{
            return $this->error(__('text.Not Found'),404);
        }
    }


    //check sizes after failed payment and continue to try
    public function check_stock(Request $request)
    {
        app()->setLocale($request->lang);
        $empty_sizes=[];
        $order=Order::find($request->order_id);
        if($order){
            $response=$this->checkIfExist($request->sizes_id,$empty_sizes);
            if($response != 'done'){
                $order->delete();
                return $response;
            }else{
                return response()->json(__('text.Done'),200);
            }
        }else{
            return response()->json('',404);
        }

    }


    public function all_orders(Request $request)
    {
        $user=$request->user();
        return $this->success(OrderResource::collection($user->orders()->where('order_status' ,'!=','completed')->where('order_status' ,'!=','canceled')->where('order_status' ,'!=','modified')->get()),'',200);

    }


    public function order_details(Request $request)
    {
        app()->setlocale($request->lang);
        $user=$request->user();
        $order=Order::find($request->order_id);

        if($order && $order->user_id == $user->id&& ($order->order_status != 'completed' || $order->order_status != 'canceled' || $order->order_status != 'modified')){


                foreach( $order->sizes()->withTrashed()->get() as $size){

                    $amount=$order->colors()->withTrashed()->where('color_id',$size->color->id)->first()->pivot->amount;
                    $tax=$size->color()->withTrashed()->first()->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax');
                    $products[]=[
                        'size' => $size->pivot->size."",
                        'size_id' => (int) $size->id,
                        'quantity' => $size->pivot->quantity."",
                        'image' => $size->color()->withTrashed()->first()->images()->first()->name,
                        'color' => $size->color()->withTrashed()->first()->color,
                        'name' => app()->getLocale()== 'ar' ? $size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_ar:$size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_en,
                        'price' => $amount + ($amount*($tax/100))."",
                    ];
                }

                $data=
                        [
                            'taxes' => $order->taxes."",
                            'order_status' => $order->order_status."",
                            'subtotal' => $order->subtotal."",
                            'shipping' => $order->shipping."",
                            'total_amount' => $order->total_amount."",
                            'products' =>$products
                        ];


            return $this->success($data,'',200);


        }else{
            return $this->error(__('text.Not Found'),404);
        }
    }


}
