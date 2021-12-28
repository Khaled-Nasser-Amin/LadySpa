<?php

namespace App\Http\Resources;

use App\Models\Size;
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
            $size=Size::find($this->size_id);

            if($size && $size->stock > 0){
                return [
                    'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                    'image' => $this->image,
                    'gallery' => $this->images->pluck('name'),
                    'type' => $this->type,
                    'id' => $this->id,
                    'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                    'price' => $size->price,
                    'sale' => $size->sale,
                    'tax' => ($this->taxes->sum('tax')*($size->sale == 0 || $size->sale == ''? $size->price:$size->sale) )/100,
                ];
            }

        }elseif(!checkCollectionActive($this) && $this->type == 'group'){

            return [
                'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
                'image' => $this->image,
                'gallery' => $this->images->pluck('name'),
                'type' => $this->type,
                'id' => $this->id,
                'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                'price' => $this->group_price,
                'sale' => $this->group_sale,
                'tax' => ($this->taxes->sum('tax')*($this->group_sale == 0 || $this->group_sale == ''? $this->group_price:$this->group_sale) )/100,
                'products' => collect(ProductGroupCollection::collection($this->child_products()->get()))->collapse()->filter(),

             ];

        }

    }
}
