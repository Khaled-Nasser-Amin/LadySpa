<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorCollection extends JsonResource
{

    public function toArray($request)
    {
        if($this->products()->where('isActive',1)->count() > 0 && $this->products()->with('sizes')->get()->pluck('sizes')->collapse()->sum('stock') > 0){
            return [
                    'id' => (int) $this->id,
                    'name' => $this->store_name ,
                    'image' => $this->image,
                    'phone' => $this->phone."",
                    'email' => $this->email,
                    'whatsapp' => $this->whatsapp."",
                    ];
        }
    }
}
