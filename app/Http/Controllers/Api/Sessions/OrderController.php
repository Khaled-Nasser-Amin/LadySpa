<?php

namespace App\Http\Controllers\Api\delivery_service_provider;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Mail\AfterOrderComplete;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Size;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use Responses;

    public function all_orders(Request $request)
    {
        $user = $request->user();
        return $this->success(OrderResource::collection($user->orders()->where('order_status', '!=', 'modified')->where('order_status', '!=', 'canceled')->where('order_status', '!=', 'completed')->get()), '', 200);
    }

    public function order_details(Request $request)
    {
        app()->setlocale($request->lang);
        $user = $request->user();
        $order = Order::find($request->order_id);

        if ($order && $order->delivery_service_provider_id == $user->id) {
            foreach ($order->vendors()->withTrashed()->get() as $vendor) {


                foreach ($order->sizes()->withTrashed()
                    ->join('colors', 'colors.id', 'sizes.color_id')
                    ->join('products', 'products.id', 'colors.product_id')
                    ->withTrashed()->where('products.user_id', $vendor->id)->get() as $size) {

                    $amount = $order->colors()->withTrashed()->where('color_id', $size->color->id)->first()->pivot->amount;
                    $tax = $size->color()->withTrashed()->first()->product()->withTrashed()->first()->taxes()->withTrashed()->sum('tax');
                    $products[] = [
                        'size' => $size->pivot->size."",
                        'size_id' => (int) $size->id,
                        'quantity' => $size->pivot->quantity."",
                        'image' => $size->color()->withTrashed()->first()->images()->first()->name,
                        'color' => $size->color()->withTrashed()->first()->color,
                        'name' => app()->getLocale() == 'ar' ? $size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_ar : $size->color()->withTrashed()->first()->product()->withTrashed()->first()->name_en,
                        'price' => $amount + ($amount * ($tax / 100))."",
                    ];
                }

                $data[] =
                    [
                        'store_name' => $vendor->store_name."",
                        'location' => $vendor->geoLocation."",
                        'phone' => $vendor->phone."",
                        'taxes' => $vendor->pivot->taxes."",
                        'subtotal' => $vendor->pivot->subtotal."",
                        'total_amount' => $vendor->pivot->total_amount."",
                        'products' => $products
                    ];


                $products = [];
            }

            return $this->success($data, $order->order_status, 200);
        } else {
            return $this->error(__('text.Not Found'), 404);
        }
    }


    public function updateOrderStatus(Request $request)
    {
        app()->setLocale($request->lang);
        $user = $request->user();
        $order = Order::find($request->order_id);
        if ($order && $order->delivery_service_provider_id == $user->id && ($order->order_status != 'completed' || $order->order_status != 'canceled' || $order->order_status != 'modified')) {
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


    protected function cancel_after_collected($order)
    {
        foreach ($order->sizes()->withTrashed()->get() as $size) {
            $order_size=$order->sizes->where('id', $size->id);
            $quantity = $order_size->pluck('pivot.quantity')->first();
            $price = $order_size->pluck('pivot.price')->first();
            $taxes = $order_size->pluck('pivot.tax')->first();
            Refund::create([
                'order_id' => $order->id,
                'vendor_id' => $size->color()->withTrashed()->first()->product()->withTrashed()->first()->user_id,
                'total_refund_amount' => ($quantity * $price) + $taxes,
                'size_id' => $size->id,
                'quantity' => $quantity,
                'price' => $price,
                'taxes' => $taxes,
                'size' => $order_size->pluck('pivot.size')->first(),
                'color' => $order->colors->where('id', $size->color()->withTrashed()->first()->id)->pluck('pivot.color')->first(),
                'subtotal_refund_amount' => $quantity * $price,
            ]);
            $size->update(['stock' => $size->stock + $quantity]);
        }

        foreach ($order->vendors()->withTrashed()->get() as $vendor) {
            $order->vendors()->updateExistingPivot($vendor->id, [
                'total_amount' => 0,
                'subtotal' => 0,
                'taxes' => 0,
            ]);

            Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get canceled'),$vendor->store_name));

        }

        $order->update(['taxes' => 0, 'subtotal' => 0, 'total_amount' => $order->shipping, 'payment_status' => 'failed', 'order_status' => 'canceled']);
    }

    protected function modify_after_collected($order, $sizes)
    {
        $sum_taxes = 0;
        $sum_total_amount = 0;
        $sum_subtotal = 0;
        foreach (collect($sizes)->toArray() as $size_id) {
            $size = Size::withTrashed()->find($size_id);
            if ($size) {
                $order_size=$order->sizes->where('id', $size->id);
                $quantity = $order_size->pluck('pivot.quantity')->first();
                $price = $order_size->pluck('pivot.price')->first();
                $taxes = $order_size->pluck('pivot.tax')->first();
                $total_refund_amount = ($quantity * $price) + $taxes;
                $vendor_id = $size->color()->withTrashed()->first()->product()->withTrashed()->first()->user_id;
                $subtotal_refund = $quantity * $price;
                Refund::create([
                    'order_id' => $order->id,
                    'vendor_id' => $vendor_id,
                    'total_refund_amount' => $total_refund_amount,
                    'size_id' => $size->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'taxes' => $taxes,
                    'size' => $order_size->pluck('pivot.size')->first(),
                    'color' => $order->colors->where('id', $size->color()->withTrashed()->first()->id)->pluck('pivot.color')->first(),
                    'subtotal_refund_amount' => $subtotal_refund,
                ]);
                $size->update(['stock' => $size->stock + $quantity]);

                $order->vendors()->updateExistingPivot($vendor_id, [
                    'total_amount' => $order->vendors->find($vendor_id)->pivot->total_amount - $total_refund_amount,
                    'subtotal' => $order->vendors->find($vendor_id)->pivot->subtotal - $subtotal_refund,
                    'taxes' => $order->vendors->find($vendor_id)->pivot->taxes - $taxes,
                ]);

                $sum_taxes += $taxes;
                $sum_total_amount += $total_refund_amount;
                $sum_subtotal += $subtotal_refund;
                $vendor=User::find($vendor_id);
                Mail::to($vendor->email)->send(new AfterOrderComplete(__('text.Your order') . $order->id . __('text.get modified'),$vendor->store_name));

            }
        }

        $order->update(['taxes' => $order->taxes - $sum_taxes, 'subtotal' => $order->subtotal - $sum_subtotal, 'total_amount' => $order->total_amount - $sum_total_amount, 'payment_status' => 'paid', 'order_status' => 'modified']);
    }


    public function all_completed_orders(Request $request)
    {
        $user = $request->user();
        $orders=Order::where('delivery_service_provider_id',$user->id)->where(function($q){
            return $q->where('order_status', 'modified')->orWhere('order_status', 'canceled')->orWhere('order_status', 'completed');
        })->get();
        return $this->success(OrderResource::collection($orders), '', 200);
    }
}
