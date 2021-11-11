<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'location' => $this->location,
            'order_status' => $this->order_status,
            'receiver_phone' => $this->receiver_phone,
            'receiver_name' => $this->receiver_first_name. " ". $this->receiver_last_name,
            'payment_way' => $this->payment_way,
            'lat_long' => $this->lat_long,
            'total' => number_format($this->total_amount,2),
            'subtotal' => number_format($this->subtotal,2),
            'shipping' => number_format($this->shipping,2),
            'taxes' => number_format($this->taxes,2),
        ];

    }
}
