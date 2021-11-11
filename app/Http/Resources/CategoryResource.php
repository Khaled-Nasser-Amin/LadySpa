<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => (int) $this->parent_id,
            'name' => app()->getLocale() == 'ar' ?$this->name_ar: $this->name_en ,
            'sub_categories' => collect(CategoryCollection::collection($this->child_categories))->filter()->all(),
            'products' =>collect(ProductCollection::collection($this->products))->filter()->all(),
        ];
    }
}
