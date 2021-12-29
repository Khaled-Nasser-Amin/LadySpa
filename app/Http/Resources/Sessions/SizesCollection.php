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
                'id' =>(int) $this->id,
                'size' => $this->size,
                'stock' => (int) $this->stock,
                'price' => number_format($this->price,2),
                'sale' => number_format($this->sale,2),
                'tax' => number_format(($this->product->taxes->sum('tax')*($this->sale == 0 || $this->sale == ''? $this->price:$this->sale) )/100,2),

            ];
        }

    }
}
