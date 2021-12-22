<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductGroupCollection extends JsonResource
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
            'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
            'image' => $this->image,
            'id' => $this->id,
            'sizes' =>$this->pivot->sizes()->get()->pluck('size'),
            'quantities' => $this->pivot->sizes()->get()->pluck('pivot.quantity'),
        ];

    }
}
