<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SizesCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->stock > 0 ){
            return [
                'id' => $this->id,
                'size' => $this->size,
                'stock' => (int) $this->stock,
            ];
        }

    }
}
