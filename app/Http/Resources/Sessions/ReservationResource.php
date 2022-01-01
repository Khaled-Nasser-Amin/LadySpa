<?php

namespace App\Http\Resources\Sessions;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $can=false;

        if($this->order_status == 'pending' && $this->payment_way == 'cash on delivery'){
            $can=true;

        }
        return [
            'id' => $this->id,
            'address' => $this->address,
            'order_status' => $this->order_status,
            'receiver_phone' => $this->receiver_phone,
            'receiver_name' => $this->receiver_name,
            'payment_way' => $this->payment_way,
            'description' => $this->description,
            'total' => number_format($this->total_amount,2),
            'subtotal' => number_format($this->subtotal,2),
            'shipping' => number_format($this->shipping,2),
            'taxes' => number_format($this->taxes,2),
            'discount' => number_format($this->discount,2),
            'can'=>$can,
        ];

    }
}
