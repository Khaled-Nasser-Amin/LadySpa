<?php

namespace App\Http\Resources\Sessions;

use Illuminate\Http\Resources\Json\JsonResource;

class AdditionsCollection extends JsonResource
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
            'id' =>(int) $this->id,
            'name' => app()->getLocale() == 'ar' ? $this->name_ar:$this->name_en,
            'price' => $this->price."",
        ];

    }
}
