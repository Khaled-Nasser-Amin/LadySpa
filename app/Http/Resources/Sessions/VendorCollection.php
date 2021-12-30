<?php

namespace App\Http\Resources\Sessions;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorCollection extends JsonResource
{

    public function toArray($request)
    {
        if($this->sessions()->where('isActive',1)->count() > 0 ){
            return [
                'id' => (int) $this->id,
                'name' => $this->store_name ,
                'image' => $this->image,
                'phone' => $this->phone."",
                'email' => $this->email,
                'geoLocation' => $this->geoLocation,
                'location' => $this->location,
                'whatsapp' => $this->whatsapp."",
                'opening_time' => date('h:i a', strtotime($this->opening_time)),
                'closing_time' =>  date('h:i a', strtotime($this->closing_time)),
            ];
        }
    }
}
