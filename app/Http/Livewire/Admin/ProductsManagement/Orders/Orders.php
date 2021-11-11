<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Orders;

use App\Models\DeliveryServiceProvider;
use App\Models\Order;
use App\Models\Size;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination,ImageTrait;
    public $search,$payment_status,$order_status,$payment_way;


    public function render()
    {
        $order=Order::find(54);
        $vendor=User::find(12);
        $delivery_service_providers=DeliveryServiceProvider::where('activation',1)->get();
        $orders=$this->search();
        return view('admin.productManagement.orders.index',compact('orders','delivery_service_providers'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function assignOrderToDelivery(DeliveryServiceProvider $delivery_service_provider,Order $order){
        Gate::authorize('isAdmin', auth()->user());
        if(($order->order_status == 'pending' && $order->payment_way == 'cash on delivery') || ($order->payment_way == 'online payment' && $order->payment_status == 'paid' && $order->order_status == 'pending')){
            $delivery_service_provider->orders()->save($order);
        }
    }


    public function cancelOrderToDelivery(Order $order){
        Gate::authorize('isAdmin', auth()->user());
        $order->update(['delivery_service_provider_id'=>Null]);
        $order->save();
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
                    ->orWhere('orders.location','like','%'.$this->search.'%')
                    ->orWhere('orders.receiver_first_name','like','%'.$this->search.'%')
                    ->orWhere('orders.receiver_last_name','like','%'.$this->search.'%')
                    ->orWhereIn('orders.receiver_first_name',explode(" ",$this->search))
                    ->orWhereIn('orders.receiver_last_name',explode(" ",$this->search));
            });
         });

        })->orderByDesc('orders.id')->latest()->paginate(10);
    }
}
