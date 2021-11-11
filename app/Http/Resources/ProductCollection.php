<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->isActive == 1 && $this->sizes->sum('stock') > 0){
            return [
                    'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                    'image' => $this->image,
                    'id' => $this->id,
                 ];
        }

    }
}
