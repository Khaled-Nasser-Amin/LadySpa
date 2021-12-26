<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Orders;

use App\Mail\AfterOrderComplete;
use App\Models\Order;
use App\Models\Size;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination,ImageTrait;
    public $search,$payment_status,$order_status,$payment_way;


    public function render()
    {
        $orders=$this->search();
        return view('admin.productManagement.orders.index',compact('orders'))->extends('admin.layouts.appLogged')->section('content');
    }


    //search and order pagination
    protected function search(){
        return  Order::
        when(auth()->user()->role != 'admin',function($q){
            return $q->join('order_vendor','order_vendor.order_id','orders.id')
            ->join('users','users.id','order_vendor.vendor_id')
            ->select('orders.*')
            ->where('order_vendor.vendor_id',auth()->user()->id);
        })
        ->where(function($q){
           return $q
           ->when($this->payment_status,function($q){
            return $q->where('orders.payment_status',$this->payment_status);
          })
          ->when($this->payment_way,function($q){
            return $q->where('orders.payment_way',$this->payment_way);
          })
          ->when($this->order_status,function($q){
            return $q->where('orders.order_status',$this->order_status);
          })
          ->where(function($q){

            $q->when(is_numeric($this->search),function ($q){
                return $q->where('orders.id',$this->search)
                    ->orWhere('orders.taxes','like','%'.$this->search.'%')
                    ->orWhere('orders.taxes','like','%'.$this->search.'%')
                    ->orWhere('orders.shipping','like','%'.$this->search.'%')
                    ->orWhere('orders.subtotal','like','%'.$this->search.'%');
            })
            ->when(!is_numeric($this->search),function ($q){
                return $q->where('orders.payment_way','like','%'.$this->search.'%')
                    ->orWhere('orders.address','like','%'.$this->search.'%')
                    ->orWhere('orders.receiver_name','like','%'.$this->search.'%')
                    ->orWhereIn('orders.receiver_name',explode(" ",$this->search));
            });
         });

        })->orderByDesc('orders.id')->latest()->paginate(10);
    }
}
