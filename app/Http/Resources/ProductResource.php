<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
                'gallary' => $this->images->pluck('name'),
                'type' => $this->type,
                'id' => $this->id,
                'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                'sizes' => collect(SizesCollection::collection($this->sizes))->filter()->all(),
             //    'is_favorite' => auth()->user()->wishList()->find($this->id) ? 1: 0,
             ];
        }elseif(!checkCollectionActive($this) && $this->type == 'group'){

            return [
                'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                'image' => $this->image,
                'gallary' => $this->images->pluck('name'),
                'type' => $this->type,
                'id' => $this->id,
                'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                'price' => $this->group_price,
                'sale' => $this->group_sale,
                'tax' => ($this->taxes->sum('tax')*($this->group_sale == 0 || $this->group_sale == ''? $this->group_price:$this->group_sale) )/100,
                'products' => ProductGroupCollection::collection($this->child_products()->get()),

                //    'is_favorite' => auth()->user()->wishList()->find($this->id) ? 1: 0,
             ];

        }

    }
}
