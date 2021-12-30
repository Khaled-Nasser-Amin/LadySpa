<?php

namespace App\Http\Resources;

use App\Models\Size;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FavoriteCollection extends JsonResource
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
            if($this->type == 'single'){
                $size=Size::find($this->pivot->size_id);
                if($size && $size->stock > 0){
                    return[
                        'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                        'image' => $this->image,
                        'type' => $this->type,
                        'id' =>(int) $this->id,
                        'size_id' =>(int) $size->id,
                        'size' => $size->size,
                    ];
                }

            }elseif($this->type == 'group' && !checkCollectionActive($this)){
               return [
                    'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                    'image' => $this->image,
                    'type' => $this->type,
                    'id' => (int) $this->id,
                    'size_id' => 0,
                    'size' =>"",
                ];
            }


        }

    }
}
