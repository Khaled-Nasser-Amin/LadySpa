<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
class ProductsGroups extends Pivot
{

 public function sizes()
 {
     return $this->hasMany(ProductsGroupSizesAndQuantity::class,'group_id','id');
 }

}
