<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\RefundGroup;
use App\Models\RefundReservation;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Xsession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index(){
        $date=$this->getDate();
        $order=auth()->user()->orders()->where('payment_status','paid');
        $reservation=auth()->user()->reservations()->where('payment_status','paid')->where('reservation_status','completed')->get();
        $reservations=$reservation->count();
        $orders=$order->get()->count();
        $total_refunds=auth()->user()->refunds->where('refund_status','not refunded yet')->sum('total_refund_amount')+auth()->user()->refund_groups->where('refund_status','not refunded yet')->sum('total_refund_amount')+auth()->user()->reservations_refunds->where('refund_status','not refunded yet')->sum('total_refund_amount');
        $products=auth()->user()->products()->where('isActive',1)->count();
        $sessions=auth()->user()->sessions()->where('isActive',1)->count();
        $inactive_products=auth()->user()->products()->where('isActive',0)->count();
        $inactive_sizes_counter=0;
        auth()->user()->products_sizes->map(function($item) use(&$inactive_sizes_counter){
            if($item->sum('stock') == 0){
                $inactive_sizes_counter+=1;
            }
            return $item->sum('stock');
        });

        $total_amount=$order->get()->sum('total_amount');
        $reservation_total_amount=$reservation->sum('total_amount');
        $current_month_orders=auth()->user()->orders()->where('payment_status','paid')->getOrdersThroughMonth($date['year'],$date['month']);
        $last_month_orders=auth()->user()->orders()->where('payment_status','paid')->getOrdersThroughMonth($date['last_year'],$date['last_month']);
        $current_month_reservations=$this->reservationThroughMonth($date['year'],$date['month']);
        $last_month_reservations=$this->reservationThroughMonth($date['last_year'],$date['last_month']);
        return view('admin.dashboard',compact('products','sessions','orders','reservation_total_amount','reservations','current_month_reservations','last_month_reservations','total_amount','current_month_orders','last_month_orders','inactive_products','inactive_sizes_counter','total_refunds'));

    }

    public function index_for_app(){
        $products=Product::where('isActive',1)->count();
        $sessions=Xsession::where('isActive',1)->count();
        $total_refunds=Refund::where('refund_status','not refunded yet')->sum('total_refund_amount')+RefundGroup::where('refund_status','not refunded yet')->sum('total_refund_amount')+RefundReservation::where('refund_status','not refunded yet')->sum('total_refund_amount');
        $users=Customer::count();
        $vendors=User::where('role' ,'!=' ,'admin')->count();
        $orders=Order::where('payment_status','paid')->count();
        $total_amount=Order::where('payment_status','paid')->sum('total_amount');
        $reservation_total_amount=Reservation::where('payment_status','paid')->where('reservation_status','completed')->sum('total_amount');
        $date=$this->getDate();
        $current_month_orders=Order::where('payment_status','paid')->getOrdersThroughMonth($date['year'],$date['month']);
        $last_month_orders=Order::where('payment_status','paid')->getOrdersThroughMonth($date['last_year'],$date['last_month']);
        $current_month_reservations=$this->reservationThroughMonthForAdmin($date['year'],$date['month']);
        $last_month_reservations=$this->reservationThroughMonthForAdmin($date['last_year'],$date['last_month']);

        return view('admin.dashboardForApp',compact('products','total_refunds','sessions','reservation_total_amount','current_month_reservations','last_month_reservations','vendors','users','orders','total_amount','current_month_orders','last_month_orders'));
    }

    protected function getDate(){
        $date['month']= Carbon::now()->month;
        $date['year']= Carbon::now()->year;
        if($date['month'] == 1){
            $date['last_month']=12;
            $date['last_year']=$date['year']-1;
        }else{
            $date['last_month']=$date['month']-1;
            $date['last_year']=$date['year'];
        }

        return $date;
    }

    protected function orderThroughMonth($year,$month)
    {

    }
    protected function reservationThroughMonth($year,$month)
    {
       return Reservation::join('reservation_times','reservation_times.reservation_id','reservations.id')
        ->select('reservations.*','reservation_times.date as date')
        ->where('reservations.vendor_id',auth()->user()->id)
        ->where('payment_status','paid')
        ->where('reservation_status','completed')
        ->whereYear('reservation_times.date',$year)
        ->whereMonth('reservation_times.date',$month)
        ->orderBy('reservation_times.date')
        ->get()
        ->groupBy(function($data) {
            //week
            return Carbon::parse($data->date)->format('W');

        });
    }
    protected function reservationThroughMonthForAdmin($year,$month)
    {
       return Reservation::join('reservation_times','reservation_times.reservation_id','reservations.id')
        ->select('reservations.*','reservation_times.date as date')
        ->where('payment_status','paid')
        ->where('reservation_status','completed')
        ->whereYear('reservation_times.date',$year)
        ->whereMonth('reservation_times.date',$month)
        ->orderBy('reservation_times.date')
        ->get()
        ->groupBy(function($data) {
            //week
            return Carbon::parse($data->date)->format('W');

        });
    }
}
