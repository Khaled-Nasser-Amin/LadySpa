<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
class ProductsGroups extends Pivot
{

 public function sizes()
 {
     return $this->belongsToMany(Size::class,'products_group_sizes_and_quantities','group_id','size_id')->withPivot(['quantity']);
 }

}
