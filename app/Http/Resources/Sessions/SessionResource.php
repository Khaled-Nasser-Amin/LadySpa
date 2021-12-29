<?php

namespace App\Http\Resources\Sessions;

use App\Models\Size;
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
        if($this->type == 'single'){
            return [
                'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                'image' => $this->image,
                'gallery' => $this->images->pluck('name'),
                'type' => $this->type,
                'id' => (int) $this->id,
                'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                'price' => number_format($this->group_price,2),
                'sale' => number_format($this->group_sale,2),
                'tax' => number_format(($this->taxes->sum('tax')*($this->group_sale == 0 || $this->group_sale == ''? $this->group_price:$this->group_sale) )/100,2),
                'products' => collect(ProductGroupCollection::collection($this->child_products()->get()))->collapse()->filter(),

             ];
        }


    }
}
