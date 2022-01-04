<?php

namespace App\Http\Controllers\Api\sessions;

use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MyFatoorahController;
use App\Http\Resources\OrderResource;
use App\Http\Resources\Sessions\ReservationResource;
use App\Mail\AfterOrderComplete;
use App\Mail\ReservationCard;
use App\Models\Addition;
use App\Models\Order;
use App\Models\Promocode;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\ReservationTime;
use App\Models\Size;
use App\Models\User;
use App\Models\Xsession;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class ReservationController extends Controller
{
    use Responses;


    public function check_promocode(Request $request)
    {
        app()->setLocale($request->lang);
        $user=$request->user();
        $discount=$this->calculatePromoCode($request->promocode,$user,$request->total_amount);
        $instance=new MyFatoorahController();
        return response()->json(['discount'=>$discount."",'online_payment_status'=>$instance->check_online_payment($request)],200);
    }


    public function reservation_shipping(Request $request)
    {
        $session=Xsession::find($request->session_id);
        if($session && $request->lat_long && $session->isActive == 1  &&  $session->external_price > 0 ){
            $customer_lat_long=explode(',',$request->lat_long);
            $calc_shipping=new ShippingController();
            $shipping_cost=$calc_shipping->calc_shipping_single($session->user->geoLocation,$customer_lat_long[0],$customer_lat_long[1]);
            return $this->success($shipping_cost,'',200);
        }else{
            return $this->error('',404);
        }


    }

    // return available times
    public function availableTime(Request $request)
    {
        app()->setLocale($request->lang);
        $session=Xsession::find($request->session_id);
        $date=$request->date;
        $type=$request->type;
        if($session && $date && $session->isActive == 1 && $type && ($type == 'outdoor' || $type == 'indoor')){
            $limit=$type == 'outdoor'? $session->user->session_rooms_limitation_outdoor : $session->user->session_rooms_limitation_indoor;
            $opening_time=$session->user->opening_time;
            $closing_time=$session->user->closing_time;
            $session_time=explode(':',$session->time);

            if($type == 'outdoor' && $session->external_price <= 0){
                return $this->error(__('text.Not Found'), 404);
            }else{

                if($date >= now()->format('Y-m-d')){
                    $times_arranged=[];

                    $this->arrangTimes($times_arranged,$opening_time,$closing_time,$session_time);
                    $rooms=[];

                   $this->availableTimesInDate($rooms,$session,$times_arranged,$limit,$date);
                   if(collect($request->times)->count() > 0){
                       $times=collect($request->times)->where('date',$date)->pluck('time');
                        foreach($times as $time){
                            foreach($rooms as $key => $room){
                                foreach($room as $index => $room_time){
                                    if($room_time == $time){
                                        unset($rooms[$key][$index]);
                                        break 2;
                                    }
                                }
                            }

                        }
                        $new_map=collect($rooms)->map(function ($room) {
                            return collect($room)->values();
                        });
                        return $this->success($new_map);

                   }
                    return $this->success($rooms);


                }else{
                    return $this->error(__('text.Not Found'), 404);
                }
            }

        }else{
            return $this->error(__('text.Not Found'), 404);

        }
    }

      //available Times In Date
    protected function availableTimesInDate(&$rooms,$session,$times_arranged,$limit,$date)
    {

        for($i=1; $i <= $limit;$i++){
            $arr=[];
            foreach($times_arranged as $time){
                $query=$this->countReservationByDate($date,$session->user_id,$time['start'],$time['end']);
                $count=$query->where('room_number',$i)->get()->count();
                if($count == 0){
                    $arr[] = date('h:i a',strtotime($time['start'])).' - '.date('h:i a',strtotime($time['end']));
                }
            }
            $rooms[]=$arr;
        }


    }

    //arrange times
    protected function arrangTimes(&$times_arranged,$opening_time,$closing_time,$session_time)
    {
        $loop=true;
        $opening_time= new Carbon($opening_time);
        $hours=$session_time[0];
        $minutes=$session_time[1];
        do{
            $start=$opening_time->format('H:i');
            if($opening_time->addHours($hours)->addMinutes($minutes)->format('H:i') <= $closing_time){
                $times_arranged[]=['start' => $start,'end' => $opening_time->format('H:i')];

            }else{
                $loop=false;
            }

        }while($loop);

    }






    // create reservation
    public function store(Request $request){
        app()->setLocale($request->lang);
        $user=$request->user();
        $request['payment_way']= strtolower($request['payment_way']) == 'online payment' ? 'online payment' : 'cash on delivery';


        //validation
        $validation=Validator::make($request->all(),$this->rules());
        if($validation->fails()){
            return response()->json($validation->errors(),404);
        }

        //check if additions or session  is active or exist
        $session=Xsession::find($request->session_id);
        $reservation_times=[];
        $additions=$request->additions;
        $validate1=$this->checkValidateSession($session,$additions,$request->type);
        $limit= $request->type == 'outdoor'? $session->user->session_rooms_limitation_outdoor : $session->user->session_rooms_limitation_indoor;
        $validate2=$this->checkIfExist($session,$request->times,$limit,$reservation_times);
        if($validate1 == 'false' || $validate2 == 'false'){
            return $this->error(__('text.Not Found'), 404);
        }
        $data=$request->except(['times','lang','additions','promocode','session_id']);
        $reservation=Reservation::create($data);
        $user->reservations()->save($reservation);
        $session->reservations()->save($reservation);
        $session->user()->first()->reservations()->save($reservation);
        $subtotal=0;
        $taxes=$session->taxes()->sum('tax');


        // calculate data
        $this->calcReservation($additions,$session,$reservation,$session->user,$reservation_times,$limit,$request->type,$taxes,$subtotal,$request->promocode,$user);

        //send mail to vendor and customer
        $this->sendEmailToVendorsAndCustomer($user,$reservation);
        $this->sendEmailToVendorsAndCustomer($session->user,$reservation);

        if($request['payment_way'] == 'online payment'){
            $payment=new MyFatoorahController();
            $data=$payment->index($reservation->total_amount,$user->name,$user->phone,$user->email,$reservation->id);
            if($data == 'error'){
                $reservation->update(['payment_way' => 'cash on delivery']);
                $reservation->save();
                return $this->success(new ReservationResource($reservation),__('text.Order created successfully'),305);
            }

            $reservation->save();

            return $this->success(array_merge(collect($data)->toArray(),collect(new ReservationResource($reservation))->toArray()),__('text.Order created successfully'),200);
        }
        return $this->success(new ReservationResource($reservation),__('text.Order created successfully'),200);
    }


    //validation
    protected function rules(){
        return [
            'times' => 'required|array|min:1',
            'additions' => 'nullable|array|min:1',
            'additions.*' => 'exists:additions,id',
            'address' => 'required|string|max:255',
            'session_id' => 'required|exists:xsessions,id',
            'payment_way' => ['required',Rule::in(['cash on delivery','online payment'])],
            'type' => ['required',Rule::in(['indoor','outdoor'])],
            'lat_long' => 'nullable',
            'times.*.date' => 'date|after:yesterday',
            'receiver_phone' => 'required|numeric',
            'receiver_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }


    //check exists
    protected function checkValidateSession($session,$additions,$type)
    {
        $additions=collect($additions);
        $intersect=$additions->intersect($session->additions->pluck('id'))->count();

        if($session && $session->isActive == 1 && $additions->count() == $intersect && ($type =='indoor' || ($type == 'outdoor' && $session->external_price > 0 ))){
            return 'true';
        }else{
            return 'false';
        }
    }

    //calc promo code
    protected function calculatePromoCode($promocode,$user,$subtotal)
    {
        $normal_code=Promocode::where('code',$promocode)->where('type_of_code','normal')->first();
        $result=checkPromoCode($user,$user->specialCode,$normal_code,$promocode,'sessions');
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



    //check exists
    protected function checkIfExist($session,$times,$limit,&$reservation_times)
    {

        $groupBy=collect($times)->groupBy(['date','time']);
        foreach($times as $time){
            $arr=[];
            $arr=explode('-',$time['time']);
            $start_time=date('H:i',strtotime($arr[0]));
            $end_time=date('H:i',strtotime($arr[1]));
            $totalDuration =  Carbon::createFromFormat('H:i', $end_time)->diffInSeconds($start_time);

            $opening_time=$session->user->opening_time;
            $closing_time=$session->user->closing_time;
            if(date('H:i',strtotime($session->time)) != gmdate('H:i',$totalDuration) || (date('H:i:s',strtotime($arr[0])) < $opening_time) || (date('H:i:s',strtotime($arr[1])) > $closing_time)){
                return 'false';
            }

            $query=$this->countReservationByDate($time['date'],$session->user_id,$start_time,$end_time);
            $group_count=$groupBy[$time['date']][$time['time']]->count();

            if( $query->get()->count()+$group_count > $limit){
                return 'false';
            }
            $count=0;
            for($i=1; $i <= $limit;$i++){
                $query2=$this->countReservationByDate($time['date'],$session->user_id,$start_time,$end_time);
                $count2=$query2->where('room_number',$i)->get()->count();
                if($count2 == 0){
                    $reservation_times[] = ['date' => $time['date'],'start_time' => $start_time,'end_time' => $end_time];
                    break;
                }else{
                    $count++;
                }
            }
            if($count == $limit){
                return 'false';
            }
        }
    }



    protected function calcReservation($additions,$session,$reservation,$vendor,&$times,$limit,$type,$taxes,&$subtotal,$promocode,$user)
    {
        if($type == 'outdoor'){
            $subtotal=$session->external_sale ?? $session->external_price;
        }else{
            $subtotal=$session->sale ?? $session->price;
        }
        $taxes=(($taxes/100)*$subtotal)*count($times);
        $this->associateAdditionsWithReservation($additions,$reservation,$subtotal);

        $discount=$this->calculatePromoCode($promocode,$user,$subtotal);

        $this->associatePromotionCode($discount,$promocode,$user);
        $subtotal=$subtotal*count($times);


        $reservation->update(['total_amount' => ($subtotal+$taxes-$discount),'subtotal' => $subtotal,'taxes' => $taxes,'discount' => $discount]);
        $reservation->save();

        if($reservation->lat_long && $type == 'outdoor'){
            $this->calculate_shipping($reservation,$vendor->geoLocation);

        }
        $this->associateTimesWithReservation($times,$reservation,$limit);

    }

    //associate Additions With Reservation
    protected function associateAdditionsWithReservation($additions,$reservation,&$subtotal)
    {
        foreach($additions as $addi){
            $addition=Addition::find($addi);
            $addition->reservations()->syncWithoutDetaching([$reservation->id => ['price'=> $addition->price,'name_ar' => $addition->name_ar,'name_en' => $addition->name_en]]);
            $subtotal+=$addition->price;
        }
    }

     //associate times With Reservation
    protected function associateTimesWithReservation($times,$reservation,$limit)
    {
        foreach($times as $time){
            for($i=1; $i <= $limit;$i++){
                $query=$this->countReservationByDate($time['date'],$reservation->vendor_id,$time['start_time'],$time['end_time']);
                $count=$query->where('room_number',$i)->get()
                ->count();
                if($count == 0){
                    ReservationTime::create([
                        'vendor_id'=>$reservation->vendor_id,
                        'reservation_id'=>$reservation->id,
                        'start_time'=>$time['start_time'],
                        'end_time'=>$time['end_time'],
                        'room_number'=>$i,
                        'date'=>$time['date'],
                ]);
                    break;
                }
            }
        }
    }


    //calculate shipping
    protected function calculate_shipping(&$reservation,$vendor_lat_long)
    {
        $reservation_lat_long=explode(',',$reservation->lat_long);
        $calc_shipping=new ShippingController();
        $shipping_cost=$calc_shipping->calc_shipping_single($vendor_lat_long,$reservation_lat_long[0],$reservation_lat_long[1]);
        $reservation->update(['shipping' => $shipping_cost,'total_amount' => $reservation->total_amount+$shipping_cost]);
        $reservation->save();
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


    //return count
    protected function countReservationByDate($date,$vendor_id,$start_time,$end_time)
    {
        return ReservationTime::where('vendor_id',$vendor_id)->where('date',$date)
        ->where(function($q) use($start_time,$end_time){
            $q->where(function($q) use($start_time){
                $q->where('start_time','<=',$start_time)
                ->where('end_time','>',$start_time);
            })->orWhere(function($q) use($end_time){
                $q->where('start_time','<',$end_time)
                ->where('end_time','>',$end_time);
            });
        });
    }

    protected function sendEmailToVendorsAndCustomer($user,$reservation)
    {
        Mail::to($user->email)->send(new ReservationCard($reservation,$user->store_name ?? $user->name));
    }






    // public function all_orders(Request $request)
    // {
    //     $user = $request->user();
    //     return $this->success(OrderResource::collection($user->orders()->where('order_status', '!=', 'modified')->where('order_status', '!=', 'canceled')->where('order_status', '!=', 'completed')->get()), '', 200);
    // }

    // public function order_details(Request $request)
    // {
    //     app()->setlocale($request->lang);
    //     $user = $request->user();
    //     $order = Order::find($request->order_id);

    //     if ($order && $order->delivery_service_provider_id == $user->id) {
    //         foreach ($order->vendors()->withTrashed()->get() as $vendor) {


    //             foreach ($order->sizes()->withTrashed()
    //                 ->join('colors', 'colors.id', 'sizes.color_id')
    //                 ->join('products', 'products.id', 'colors.product_id')
    //                 ->withTrashed()->where('products.user_id', $vendor->id)->get() as $size) {

    //                 $amount = $order->colors()->withTrashed()->where('color_id', $size->color->id)->first()->pivot->amount;
    //                 $tax = $size->color()->withTrashed()->first()->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax');
    //                 $products[] = [
    //                     'size' => $size->pivot->size."",
    //                     'size_id' => (int) $size->id,
    //                     'quantity' => $size->pivot->quantity."",
    //                     'image' => $size->color()->withTrashed()->first()->images()->first()->name,
    //                     'color' => $size->color()->withTrashed()->first()->color,
    //                     'name' => app()->getLocale() == 'ar' ? $size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_ar : $size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_en,
    //                     'price' => $amount + ($amount * ($tax / 100))."",
    //                 ];
    //             }

    //             $data[] =
    //                 [
    //                     'store_name' => $vendor->store_name."",
    //                     'location' => $vendor->geoLocation."",
    //                     'phone' => $vendor->phone."",
    //                     'taxes' => $vendor->pivot->taxes."",
    //                     'subtotal' => $vendor->pivot->subtotal."",
    //                     'total_amount' => $vendor->pivot->total_amount."",
    //                     'products' => $products
    //                 ];


    //             $products = [];
    //         }

    //         return $this->success($data, $order->order_status, 200);
    //     } else {
    //         return $this->error(__('text.Not Found'), 404);
    //     }
    // }


    // public function updateOrderStatus(Request $request)
    // {
    //     app()->setLocale($request->lang);
    //     $user = $request->user();
    //     $order = Order::find($request->order_id);
    //     if ($order && $order->delivery_service_provider_id == $user->id && ($order->order_status != 'completed' || $order->order_status != 'canceled' || $order->order_status != 'modified')) {
    //         if ($order->order_status == 'pending') {
    //             $order->update(['order_status' => 'proccessing']);
    //         } elseif ($order->order_status == 'proccessing') {
    //             $order->update(['order_status' => 'collected']);
    //         } elseif ($order->order_status == 'collected') {
    //             if ($request->status  && $request->status == 'cancel') {
    //                 $this->cancel_after_collected($order);
    //             } elseif ($request->status  && $request->status == 'modified' && $request->sizes_id) {
    //                 $this->modify_after_collected($order, $request->sizes_id);
    //             } elseif (!$request->status) {
    //                 $order->update(['order_status' => 'completed']);
    //                 if ($order->payment_way == 'cash on delivery') {
    //                     $order->update(['payment_status' => 'paid']);
    //                 }
    //                 foreach ($order->vendors()->withTrashed()->get() as $vendor) {
    //                     Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get completed'),$vendor->store_name));
    //                 }
    //             }
    //         }

    //         $order->save();
    //         return response()->json('', 200);
    //     } else {
    //         return $this->error(__('text.Not Found'), 404);
    //     }
    // }


    // protected function cancel_after_collected($order)
    // {
    //     foreach ($order->sizes()->withTrashed()->get() as $size) {
    //         $order_size=$order->sizes->where('id', $size->id);
    //         $quantity = $order_size->pluck('pivot.quantity')->first();
    //         $price = $order_size->pluck('pivot.price')->first();
    //         $taxes = $order_size->pluck('pivot.tax')->first();
    //         Refund::create([
    //             'order_id' => $order->id,
    //             'vendor_id' => $size->color()->withTrashed()->first()->product()->withTrashed()->first()->user_id,
    //             'total_refund_amount' => ($quantity * $price) + $taxes,
    //             'size_id' => $size->id,
    //             'quantity' => $quantity,
    //             'price' => $price,
    //             'taxes' => $taxes,
    //             'size' => $order_size->pluck('pivot.size')->first(),
    //             'color' => $order->colors->where('id', $size->color()->withTrashed()->first()->id)->pluck('pivot.color')->first(),
    //             'subtotal_refund_amount' => $quantity * $price,
    //         ]);
    //         $size->update(['stock' => $size->stock + $quantity]);
    //     }

    //     foreach ($order->vendors()->withTrashed()->get() as $vendor) {
    //         $order->vendors()->updateExistingPivot($vendor->id, [
    //             'total_amount' => 0,
    //             'subtotal' => 0,
    //             'taxes' => 0,
    //         ]);

    //         Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get canceled'),$vendor->store_name));

    //     }

    //     $order->update(['taxes' => 0, 'subtotal' => 0, 'total_amount' => $order->shipping, 'payment_status' => 'failed', 'order_status' => 'canceled']);
    // }

    // protected function modify_after_collected($order, $sizes)
    // {
    //     $sum_taxes = 0;
    //     $sum_total_amount = 0;
    //     $sum_subtotal = 0;
    //     foreach (collect($sizes)->toArray() as $size_id) {
    //         $size = Size::withTrashed()->find($size_id);
    //         if ($size) {
    //             $order_size=$order->sizes->where('id', $size->id);
    //             $quantity = $order_size->pluck('pivot.quantity')->first();
    //             $price = $order_size->pluck('pivot.price')->first();
    //             $taxes = $order_size->pluck('pivot.tax')->first();
    //             $total_refund_amount = ($quantity * $price) + $taxes;
    //             $vendor_id = $size->color()->withTrashed()->first()->product()->withTrashed()->first()->user_id;
    //             $subtotal_refund = $quantity * $price;
    //             Refund::create([
    //                 'order_id' => $order->id,
    //                 'vendor_id' => $vendor_id,
    //                 'total_refund_amount' => $total_refund_amount,
    //                 'size_id' => $size->id,
    //                 'quantity' => $quantity,
    //                 'price' => $price,
    //                 'taxes' => $taxes,
    //                 'size' => $order_size->pluck('pivot.size')->first(),
    //                 'color' => $order->colors->where('id', $size->color()->withTrashed()->first()->id)->pluck('pivot.color')->first(),
    //                 'subtotal_refund_amount' => $subtotal_refund,
    //             ]);
    //             $size->update(['stock' => $size->stock + $quantity]);

    //             $order->vendors()->updateExistingPivot($vendor_id, [
    //                 'total_amount' => $order->vendors->find($vendor_id)->pivot->total_amount - $total_refund_amount,
    //                 'subtotal' => $order->vendors->find($vendor_id)->pivot->subtotal - $subtotal_refund,
    //                 'taxes' => $order->vendors->find($vendor_id)->pivot->taxes - $taxes,
    //             ]);

    //             $sum_taxes += $taxes;
    //             $sum_total_amount += $total_refund_amount;
    //             $sum_subtotal += $subtotal_refund;
    //             $vendor=User::find($vendor_id);
    //             Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get modified'),$vendor->store_name));

    //         }
    //     }

    //     $order->update(['taxes' => $order->taxes - $sum_taxes, 'subtotal' => $order->subtotal - $sum_subtotal, 'total_amount' => $order->total_amount - $sum_total_amount, 'payment_status' => 'paid', 'order_status' => 'modified']);
    // }


    // public function all_completed_orders(Request $request)
    // {
    //     $user = $request->user();
    //     $orders=Order::where('delivery_service_provider_id',$user->id)->where(function($q){
    //         return $q->where('order_status', 'modified')->orWhere('order_status', 'canceled')->orWhere('order_status', 'completed');
    //     })->get();
    //     return $this->success(OrderResource::collection($orders), '', 200);
    // }
}
