<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory,SoftDeletes;


    protected $guarded=[''];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reviews(){
        return $this->belongsToMany(Customer::class,'product_reviews')->withPivot('review','comment')->withTimestamps();
    }
    public function wishList(){
        return $this->belongsToMany(Customer::class,'wish_list');
    }


    public function sizes(){
        return $this->hasMany(Size::class);
    }

    public function orders(){
        return $this->belongsToMany(Order::class,'orders_products')->withPivot(['name_ar','name_en','image']);

    }

    public function taxes(){
        return $this->belongsToMany(Tax::class,'products_taxes');
    }

    public function groups(){
        return $this->belongsToMany(Product::class,'products_groups','child_product_id','parent_product_id')->using(ProductsGroups::class)->withPivot(['id']);
    }

    //this relation about existing product in table groups
    public function child_products(){
        return $this->belongsToMany(Product::class,'products_groups','parent_product_id','child_product_id')->using(ProductsGroups::class)->withPivot(['id']);
    }


    public function getSlugAttribute($value){
        return Str::slug($value);
    }

    public function getImageAttribute($value){
        return asset('images/products/'.$value);
    }
    public function getBannerAttribute($value){
        return asset('images/products/'.$value);
    }
    public function images(){
        return $this->morphMany(Images::class,'images');
    }

}
