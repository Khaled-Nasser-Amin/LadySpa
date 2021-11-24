<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Xsession extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded=[''];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function wishList(){
        return $this->belongsToMany(Customer::class,'wish_list');
    }



    public function taxes(){
        return $this->belongsToMany(Tax::class,'sessions_taxes');
    }



    public function getSlugAttribute($value){
        return Str::slug($value);
    }

    public function getImageAttribute($value){
        return asset('images/sessions/'.$value);
    }
    public function getBannerAttribute($value){
        return asset('images/sessions/'.$value);
    }

    public function images(){
        return $this->morphMany(Images::class,'images');
    }
}
