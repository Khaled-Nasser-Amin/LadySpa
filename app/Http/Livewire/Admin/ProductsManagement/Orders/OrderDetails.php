<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Orders;

use App\Mail\AfterOrderComplete;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundGroup;
use App\Models\Size;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class OrderDetails extends Component
{
    use WithPagination,ImageTrait;
    public $order;

    protected $listeners=['cancelOrder'];
    public function render()
    {
        return view('components.admin.orders.order-details');
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


    public function holdOrder(){
        Gate::authorize('isAdmin');

        $order=$this->order;
        if ($order && ($order->order_status != 'completed' || $order->order_status != 'pending' || $order->order_status != 'canceled' || $order->order_status != 'modified')) {
            if($order->hold == 0){
                $order->update(['hold' => 1]);
                $this->dispatchBrowserEvent('error', __('text.Order is pending'));
            }else{
                $order->update(['hold' => 0]);

            }

        }
    }

    public function cancel(){
        $this->emit('confirmCancel');
    }
    public function cancelOrder(){
        Gate::authorize('isAdmin');

        $order=$this->order;
        if ($order) {
            if($order->order_status == 'pending' && $order->payment_way == 'cash on delivery'){
                $this->returnSizesToStock($order);
                $order->update(['payment_status' => 'failed', 'order_status' => 'canceled']);
                $this->dispatchBrowserEvent('success', __('text..Order Canceled Successfully'));

            }

            elseif($order->order_status == 'processing' || $order->order_status == 'shipping' || ($order->order_status == 'completed' && $order->updated_at->addDays(10) > now())){
                $this->refundOrder($order);
            }

        }
    }



     //cancel order
    public function returnSizesToStock($order){
        foreach($order->sizes()->withTrashed()->get() as $size){
            $size->update(['stock' => $size->stock+$size->pivot->quantity]);
        }


        foreach($order->group_products_sizes()->withTrashed()->get() as $size){
            $size->update(['stock' => ($size->stock+$size->pivot->quantity)]);
        }

    }




    protected function refundOrder($order)
    {
        Gate::authorize('isAdmin');

        foreach ($order->sizes()->withTrashed()->get() as $size) {
            $quantity = $size->pivot->quantity;
            $price =$size->pivot->price;
            $taxes = $size->pivot->tax;
            Refund::create([
                'order_id' => $order->id,
                'vendor_id' => $size->product()->withTrashed()->first()->user_id,
                'total_refund_amount' => ($quantity * $price) + $taxes,
                'size_id' => $size->id,
                'quantity' => $quantity,
                'price' => $price,
                'taxes' => $taxes,
                'size' => $size->size,
                'subtotal_refund_amount' => $quantity * $price,
            ]);
            $size->update(['stock' => $size->stock + $quantity]);
        }
        foreach ($order->group_products()->withTrashed()->get() as $product) {
            $quantity = $product->pivot->quantity;
            $price =$product->pivot->price;
            $taxes = $product->pivot->tax;
            RefundGroup::create([
                'order_id' => $order->id,
                'vendor_id' => $product->withTrashed()->first()->user_id,
                'total_refund_amount' => ($quantity * $price) + $taxes,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'taxes' => $taxes,
                'subtotal_refund_amount' => $quantity * $price,
            ]);
        }
        foreach($order->group_products_sizes()->withTrashed()->get() as $size){
            $size->update(['stock' => ($size->stock+$size->pivot->quantity)]);
        }
        foreach ($order->vendors()->withTrashed()->get() as $vendor) {
            $order->vendors()->updateExistingPivot($vendor->id, [
                'total_amount' => 0,
                'subtotal' => 0,
                'taxes' => 0,
            ]);

            Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get canceled'),$vendor->store_name));

        }

        $order->update(['payment_status' => 'failed', 'order_status' => 'refund']);
        session()->flash('danger', __('text.Order Refunded Successfully'));

    }

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



}
