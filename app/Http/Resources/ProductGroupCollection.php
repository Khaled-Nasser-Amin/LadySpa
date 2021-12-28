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
        $arr=[];
        foreach($this->sizes as $size){
            if($size->stock > 0){
                $arr[]=[
                    'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                    'image' => $this->image,
                    'type' => $this->type,
                    'id' => $this->id,
                    'size_id' => $size->id,
                    'size' => $size->size,
                ];
            }

        }
        return $arr;

    }
}
