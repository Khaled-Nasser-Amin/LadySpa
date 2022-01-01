<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Reservations;

use App\Mail\AfterOrderComplete;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundGroup;
use App\Models\ReservationTime;
use App\Models\Size;
use App\Models\User;
use App\Traits\ImageTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\VarDumper\Cloner\Data;

class ReservationDetails extends Component
{
    use WithPagination,ImageTrait;
    public $reservation,$date,$date_time,$rooms=[],$reservationTime,$time;

    protected $listeners=['cancelReservation'];
    public function render()
    {
        return view('components.admin.reservations.reservation-details');
    }

    public function edit(ReservationTime $reservationTime)
    {
        $this->reservationTime=$reservationTime;
        $this->date=date('Y-m-d',strtotime($reservationTime->date));
        $this->date_time=$reservationTime->date.' '.date('H:i a',strtotime($reservationTime->start_time)).' - '.date('H:i a',strtotime($reservationTime->end_time));

        $reservation=$reservationTime->reservation()->first();
        $session=$reservation->session()->withTrashed()->first();
        $this->availableTime($session,$this->date,$reservation->type,$reservationTime->start_time);
    }

    public function updatedDate()
    {
        $this->validate(['date' => 'required|date|date_format:Y-m-d|after:yesterday']);
        $reservation=$this->reservationTime->reservation()->first();
        $session=$reservation->session()->withTrashed()->first();
        $this->availableTime($session,$this->date,$reservation->type,$this->reservationTime->start_time);
    }

    public function updateOrderStatus()
    {
        Gate::authorize('isAdmin');
        $order=$this->order;
        if($order->hold == 1 ){
            $order->update(['hold' => 0]);
        }
        if ($order && ($order->order_status != 'completed' && $order->order_status != 'canceled' && $order->order_status != 'modified')) {
            if ($order->order_status == 'pending') {
                $order->update(['order_status' => 'processing']);
            } elseif ($order->order_status == 'processing') {
                $order->update(['order_status' => 'shipping']);
            } elseif ($order->order_status == 'shipping') {

                $order->update(['order_status' => 'completed']);
                if ($order->payment_way == 'cash on delivery') {
                    $order->update(['payment_status' => 'paid']);
                }
                foreach ($order->vendors()->withTrashed()->get() as $vendor) {
                    Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get completed'),$vendor->store_name));
                }

            }

            $order->save();
            $this->dispatchBrowserEvent('success', __('text.Order Updated Successfully'));
        }
    }

    // return available times
    protected function availableTime($session,$date,$type,$start_time)
    {
        $date=$date;
        $type=$type;
        if($session && $date && $session->isActive == 1 && $type && ($type == 'outdoor' || $type == 'indoor')){

            $limit=$type == 'outdoor'? $session->user->session_rooms_limitation_outdoor : $session->user->session_rooms_limitation_indoor;
            $opening_time=$session->user->opening_time;
            $closing_time=$session->user->closing_time;
            $session_time=explode(':',$session->time);
            if(now() < date('Y-m-d H:i:s',strtotime($date.' '.$start_time))){
                $times_arranged=[];

                $this->arrangTimes($times_arranged,$opening_time,$closing_time,$session_time);
                $this->rooms=[];
                $this->availableTimesInDate($this->rooms,$session,$times_arranged,$limit,$date);
            }
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


    //select time
    protected function selectselectTime($code){
        dd($code,$this->time);
    }



















    public function cancel(){
        $this->emit('confirmCancel');
    }
    public function cancelReservation(){
        Gate::authorize('isAdmin');

        $order=$this->order;
        if ($order) {
            if($order->order_status == 'pending' && $order->payment_way == 'cash on delivery'){
                $this->returnSizesToStock($order);
                $order->delete();
                session()->flash('danger', __('text.Order Deleted Successfully'));
                $this->redirect(route('admin.orders'));
            }

            elseif($order->order_status == 'processing' || $order->order_status == 'shipping' || ($order->order_status == 'completed' && $order->updated_at->addDays(10) > now())){
                $this->refundOrder($order);
            }

        }
    }


}
