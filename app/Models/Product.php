<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=[
        'name_ar', 'name_en',
        'slug', 'description_ar',
        'description_en','image','banner','featured','reviews','isActive','featured_slider'
    ];


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
    public function getSlugAttribute($value){
        return Str::slug($value);
    }

    public function getImageAttribute($value){
        return asset('images/products/'.$value);
    }

}
