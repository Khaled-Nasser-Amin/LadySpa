<?php

namespace App\Http\Resources\Sessions;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->type == 'outdoor'){
            $price=$this->external_price;
            $sale=$this->external_sale;
        }else{
            $price=$this->price;
            $sale=$this->sale;
        }

        return [
            'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
            'image' => $this->image,
            'gallery' => $this->images->pluck('name'),
            'id' => (int) $this->id,
            'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
            'price' => $price."",
            'sale' =>  $sale."",
            'tax' => ($this->taxes->sum('tax')*($sale == 0 || $sale == ''? $price:$sale) )/100 ."",
            'additions' => collect(AdditionsCollection::collection($this->additions()->get()))->filter(),

            ];

    }
}
