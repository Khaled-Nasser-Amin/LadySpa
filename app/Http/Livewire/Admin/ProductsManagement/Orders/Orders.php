<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Orders;

use App\Mail\AfterOrderComplete;
use App\Models\DeliveryServiceProvider;
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
        $order=Order::find(54);
        $vendor=User::find(12);
        $orders=$this->search();
        return view('admin.productManagement.orders.index',compact('orders'))->extends('admin.layouts.appLogged')->section('content');
    }

    public function updateOrderStatus(Order $order)
    {
        if ($order && ($order->order_status != 'completed' || $order->order_status != 'canceled' || $order->order_status != 'modified')) {
            if ($order->order_status == 'pending') {
                $order->update(['order_status' => 'proccessing']);
            } elseif ($order->order_status == 'proccessing') {
                $order->update(['order_status' => 'collected']);
            } elseif ($order->order_status == 'collected') {
                if ($request->status  && $request->status == 'cancel') {
                    $this->cancel_after_collected($order);
                } elseif ($request->status  && $request->status == 'modified' && $request->sizes_id) {
                    $this->modify_after_collected($order, $request->sizes_id);
                } elseif (!$request->status) {
                    $order->update(['order_status' => 'completed']);
                    if ($order->payment_way == 'cash on delivery') {
                        $order->update(['payment_status' => 'paid']);
                    }
                    foreach ($order->vendors()->withTrashed()->get() as $vendor) {
                        Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get completed'),$vendor->store_name));
                    }
                }
            }

            $order->save();
            return response()->json('', 200);
        } else {
            return $this->error(__('text.Not Found'), 404);
        }
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
