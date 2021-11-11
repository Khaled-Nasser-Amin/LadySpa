<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyFatoorahController;
use App\Http\Resources\OrderResource;
use App\Mail\EmptyStockSize;
use App\Mail\OrderCard;
use App\Models\Order;
use App\Models\Product;
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

    public function store(Request $request){
        app()->setLocale($request->lang);
        $user=$request->user();
        $request['payment_way']= strtolower($request['payment_way']) == 'online payment' ? 'online payment' : 'cash on delivery';
        $validation=Validator::make($request->all(),$this->rules());
        if($validation->fails()){
            return response()->json($validation->errors(),404);
        }
        $sizes_id=collect($request->sizes_id)->groupBy('id')->map(function($item){
            return [
                'id' =>$item->first()['id'],
                'quantity' => $item->sum('quantity')
            ];
        });

         //check if size or product or color is active or exist
         $empty_sizes=[];
         $validate=$this->checkIfExist($sizes_id,$empty_sizes);
         if($validate != 'done'){
             return $validate;
         }

        $data=$request->except(['sizes_id','lang']);
        $order=Order::create($data);
        $user=$request->user();
        $user->orders()->save($order);
        $subtotal=0;
        $taxes=0;
        $vendors=[];
        $products=[];
        $colors=[];


        // calculate data
        $this->calcOrder($sizes_id,$subtotal,$taxes,$order,$products,$colors,$vendors);


        //associate data with order
        $this->associateDataWithOrder($vendors,$order,$products,$colors,$subtotal,$taxes);


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

    // associate sizes with order and calculate stock and taxes, subtotal , total_amount
    protected function calcOrder(&$sizes_id,&$subtotal,&$taxes,&$order,&$products,&$colors,&$vendors){
        foreach($sizes_id as $row){
            $size=Size::find($row['id']);
            $finalPrice=$size->color->sale == 0 ? $size->color->price : $size->color->sale;
            $subtotal += ( $finalPrice* $row['quantity']);
            $tax=$size->color->product->taxes->sum('tax') == 0 ? 0:(($finalPrice* $size->color->product->taxes->sum('tax'))/100)*$row['quantity'];
            $taxes  += $tax;
            $vendors[]=['vendor_id'=>$size->color->product->user_id,'tax' => $tax,'subtotal'=>( $finalPrice* $row['quantity'])];
            $colors[]=['color_id' => $size->color->id,'quantity' => $row['quantity'] , 'amount' =>$finalPrice ,'total_amount' => $finalPrice*$row['quantity'],'color'=> $size->color->color ];
            $size->order()->syncWithoutDetaching([$order->id => ['quantity' => $row['quantity'],'size' => $size->size,'price'=> $finalPrice ,'tax'=>$tax]]);
            $size->update(['stock' => ($size->stock-$row['quantity'])]);
            $products[]=['product_id'=>$size->color->product_id];

        }
    }


    //validation
    protected function rules(){
        return [
            'sizes_id' => 'required|array|min:1',
            'location' => 'required|string|max:255',
            'payment_way' => ['required',Rule::in(['cash on delivery','online payment'])],
            'lat_long' => 'required',
            'receiver_phone' => 'required|numeric',
            'receiver_first_name' => 'required|string|max:255',
            'receiver_last_name' => 'required|string|max:255',
        ];
    }

    //validation if exists in  database
    protected function checkIfExist($sizes_id,&$empty_sizes){
        foreach($sizes_id as $row){
            $size=Size::find($row['id']);
            if(!$size){
                return $this->error(__('text.Not Found'),404);
            }elseif($size->product()->isActive == 0 || !$size->color->product){
                return $this->error(__('text.Product is inactive'),403);
            }elseif($size->stock <= 0){
                return $this->error(__('text.Out of Stock'),402);
            }elseif($row['quantity'] > $size->stock){
                return $this->error(__('text.Not Enough'),401);
            }
            elseif($row['quantity'] == $size->stock){
                $empty_sizes[]=$size;
            }
            elseif(!$size->color){
                return $this->error(__('text.Color is inactive'),400);
            }
        }
        return 'done';
    }

    //associate everything with order
    protected function associateDataWithOrder($vendors,$order,$products,$colors,$subtotal,$taxes){
        $order->update(['total_amount' => $subtotal+$taxes,'subtotal' => $subtotal,'taxes' => $taxes]);
        $order->save();
        $this->associateProducts($products,$order);
        $this->associateColors($colors,$order);
        $this->associateVendors($vendors,$order);

    }



    //associate vendors with order
    protected function associateVendors($vendors,$order){
        $collect=collect($vendors);
        $vendors_id=$collect->pluck('vendor_id')->unique();
        foreach($vendors_id as $id){
            $vendor=User::findOrFail($id);
            $geoLocation=explode(',',$vendor->geoLocation);
            $vendors_lat_long[]=['lat' => $geoLocation[0],'long' => $geoLocation[1]];
            $tax=$collect->where('vendor_id',$id)->sum('tax');
            $subtotal=$collect->where('vendor_id',$id)->sum('subtotal');
            $order->vendors()->syncWithoutDetaching([$id => ['taxes' =>$tax,'subtotal'=>$subtotal,'total_amount'=>$subtotal+$tax]]);
            $this->sendEmailToVendors($vendor,$order);
        }


        //calculate shipping
        $this->calculate_shipping($order,$vendors_lat_long);

        //send email to lady_store
        if($vendors_id->search(1) == 0){
            $vendor=User::findOrFail(1);
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

    //associate colors with order
    protected function associateColors($colors,$order){
        $collect=collect($colors);
        $colors_id=$collect->pluck('color_id')->unique();
        foreach($colors_id as $id){
            $quantity=$collect->where('color_id',$id)->sum('quantity');
            $amount=$collect->firstWhere('color_id',$id)['amount'];
            $color=$collect->firstWhere('color_id',$id)['color'];
            $total_amount=$collect->where('color_id',$id)->sum('total_amount');
            $order->colors()->syncWithoutDetaching([$id => ['quantity' =>$quantity,'amount'=>$amount,'total_amount'=>$total_amount,'color'=>$color]]);

        }
    }


    //send mail to all vendors in this order with order card
    protected function sendEmailToVendors($vendor,$order){
        Mail::to($vendor->email)->send(new OrderCard($order,$vendor));
    }


    //send mail to vendor if his size become out of stock
    protected function sendEmailToVendorsAfterEmptyStock($empty_sizes){

        foreach($empty_sizes as $size){
            $product_name=app()->getLocale() == 'ar' ? $size->color->product->name_ar: $size->color->product->name_en;
            $vendor_name=$size->color->product->user->store_name;
            $vendor_email=$size->color->product->user->email;
            Mail::to($vendor_email)->send(new EmptyStockSize($product_name,$vendor_name,$size->color->color,$size->size));
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
