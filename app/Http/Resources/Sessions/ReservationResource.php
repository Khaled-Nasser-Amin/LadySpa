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
        return [
            'id' => $this->id,
            'address' => $this->address,
            'reservation_status' => $this->reservation_status,
            'receiver_phone' => $this->receiver_phone,
            'receiver_name' => $this->receiver_name,
            'payment_way' => $this->payment_way,
            'description' => $this->description,
            'total' => $this->total_amount."",
            'subtotal' => $this->subtotal."",
            'shipping' => $this->shipping."",
            'taxes' => $this->taxes."",
            'discount' => $this->discount."",
        ];

    }
}
