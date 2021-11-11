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
        return [
           'name' =>  app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
           'image' => $this->image,
           'id' => $this->id,
           'description' => app()->getLocale() == 'ar' ? ($this->description_ar ?? ''):($this->description_en ?? ''),
           'type_of_fabric' => $this->typeOfFabric,
           'type_of_sleeve' => $this->typeOfSleeve,
           'additions' => $this->additions,
           'is_favorite' => auth()->user()->wishList()->find($this->id) ? 1: 0,
           'colors' =>collect(ColorsCollection::collection($this->colors))->filter()->all(),
           'products' => ProductCollection::collection($this->user->products->where('featured',1))
        ];
    }
}
