<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=[];
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function order(){
        return $this->belongsToMany(Order::class,'orders_sizes','size_id','order_id')->withPivot(['quantity','size','price','tax','amount','total_amount']);
    }

    public function groups(){
        return $this->belongsToMany(ProductsGroups::class,'products_group_sizes_and_quantities','size_id','group_id')->withPivot(['quantity']);
    }

    public function order_group_products_sizes(){
        return $this->belongsToMany(Order::class,'orders_group_products_sizes','size_id','order_id')->withPivot(['product_id','quantity','size']);
    }

}
