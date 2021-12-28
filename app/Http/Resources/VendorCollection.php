<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorCollection extends JsonResource
{

    public function toArray($request)
    {
        if($this->products()->where('isActive',1)->count() > 0 && $this->products_sizes()->get()->sum('stock')> 0){
            return [
                'id' => (int) $this->id,
                'name' => $this->store_name ,
                'image' => $this->image,
                'phone' => $this->phone."",
                'email' => $this->email,
                'geoLocation' => $this->geoLocation,
                'location' => $this->location,
                'whatsapp' => $this->whatsapp."",
                'opening_time' => date('h:i:s a', strtotime($this->opening_time)),
                'closing_time' =>  date('h:i:s a', strtotime($this->closing_time)),
            ];
        }
    }
}
