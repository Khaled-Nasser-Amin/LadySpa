<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
       $date=$this->getDate();
        $current_month_orders=auth()->user()->orders()->where('payment_status','paid')->getOrdersThroughMonth($date['year'],$date['month']);
        $last_month_orders=auth()->user()->orders()->where('payment_status','paid')->getOrdersThroughMonth($date['last_year'],$date['last_month']);
        $orders=Order::where('payment_status','paid')->count();
        $total_refunds=auth()->user()->refunds->where('refund_status','not refunded yet')->sum('total_refund_amount');
        $products=Product::where('isActive',1)->count();
        $inactive_products=auth()->user()->products()->where('isActive',0)->count();
        $inactive_sizes_counter=0;
        auth()->user()->products_sizes->map(function($item) use(&$inactive_sizes_counter){
            if($item->sum('stock') == 0){
                $inactive_sizes_counter+=1;
            }
            return $item->sum('stock');
        });
        $users=Customer::count();
        $current_month_orders=Order::where('payment_status','paid')->getOrdersThroughMonth($date['year'],$date['month']);
        $last_month_orders=Order::where('payment_status','paid')->getOrdersThroughMonth($date['last_year'],$date['last_month']);


        $total_amount=Order::where('payment_status','paid')->sum('total_amount');
        return view('admin.dashboard',compact('products','users','orders','total_amount','current_month_orders','last_month_orders','inactive_products','inactive_sizes_counter','total_refunds'));

    }

    protected function getDate(){
        $date['month']= Carbon::now()->month;
        $date['year']= Carbon::now()->year;
        if($date['month'] == 1){
            $date['last_month']=12;
            $date['last_year']=$date['year']-1;
        }else{
            $date['last_month']=$date['month']-1;
            $date['last_year']=$date['year']-1;
        }

        return $date;
    }
}
