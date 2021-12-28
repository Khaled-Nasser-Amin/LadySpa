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
        if($this->isActive == 1){
            $arr=[];

            if($this->type == 'single'){
                foreach($this->sizes as $size){
                    if($size->stock > 0){
                        $arr[]=[
                            'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                            'image' => $this->image,
                            'type' => $this->type,
                            'id' =>(int) $this->id,
                            'size_id' =>(int) $size->id,
                            'size' => $size->size,
                        ];
                    }

                }
            }elseif($this->type == 'group'){
               $arr[]= [
                    'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                    'image' => $this->image,
                    'type' => $this->type,
                    'id' => (int) $this->id,
                    'size_id' => 0,
                    'size' =>"",
                ];
            }


            return $arr;
        }

    }
}
