<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;
    protected $guarded=[];


    //single product with order
    public function sizes(){
        return $this->belongsToMany(Size::class,'orders_sizes','order_id','size_id')->withPivot(['quantity','size','price','tax','amount','total_amount']);
    }

    public function products(){
        return $this->belongsToMany(Product::class,'orders_products')->withPivot(['name_ar','name_en','image']);

    }

    //group products with order
    public function group_products(){
        return $this->belongsToMany(Product::class,'orders_group_products')->withPivot(['quantity','price','tax','amount','total_amount']);

    }

    public function group_products_sizes(){
        return $this->belongsToMany(Size::class,'orders_group_products_sizes','order_id','size_id')->withPivot(['product_id','quantity','size']);
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'user_id','id');

    }

    public function refunds(){
        return $this->hasMany(Refund::class);

    }

    public function vendors(){
        return $this->belongsToMany(User::class,'order_vendor','order_id','vendor_id');
    }

    public function scopeGetOrdersThroughMonth($q,$year,$month){
        return $q->whereYear('created_at',$year)->whereMonth('created_at',$month)
        ->orderBy('created_at')->get()->groupBy(function($data) {
            //week
            return Carbon::parse($data->created_at)->format('W');

        });
    }

    public function transaction(){
        return $this->hasOne(Transaction::class);
    }

}
