<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ColorsCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->sizes->sum('stock') > 0){
            return [
                'color' => $this->color,
                'images' => $this->images->pluck('name'),
                'price' => $this->price,
                'sale' => $this->sale,
                'tax' => ($this->product->taxes->sum('tax')*($this->sale == 0 || $this->sale == ''? $this->price:$this->sale) )/100,
                'sizes' => collect(SizesCollection::collection($this->sizes))->filter()->all(),
            ];
        }

    }
}
