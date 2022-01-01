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
                    'id' =>(int) $this->id,
                    'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
                    'price' => number_format($size->price,2),
                    'sale' => number_format($size->sale,2),
                    'tax' => number_format(($this->taxes->sum('tax')*($size->sale == 0 || $size->sale == ''? $size->price:$size->sale) )/100,2),
                    'stock' => (int) $size->stock,
                    'size_id' => (int) $size->id,
                ];
            }

        }elseif(!checkCollectionActive($this) && $this->type == 'group'){
            $stock=[];
            foreach($this->child_products()->get() as $child){
                foreach($child->pivot->sizes()->get() as $row){
                    $stock[]=(int) ($row->stock/$row->pivot->quantity);
                }
            }

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
                'stock' =>(int) min($stock),
                'size_id' =>(int) 0,
             ];

        }

    }
}
