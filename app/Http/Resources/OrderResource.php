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
            'lat_long' => $this->lat_long,
            'total' => $this->total_amount."",
            'subtotal' => $this->subtotal."",
            'shipping' => $this->shipping."",
            'taxes' => $this->taxes."",
            'discount' => $this->discount."",
            'can'=>$can,
        ];

    }
}
