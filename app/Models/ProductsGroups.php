<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
class ProductsGroups extends Pivot
{

    protected $casts = [
        'sizes' => 'array'
    ];

}
