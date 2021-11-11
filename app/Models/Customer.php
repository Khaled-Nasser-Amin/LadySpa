<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'phone',
        'code',
        'activation'
    ];

    protected $hidden = [
        'password',
        'code',
    ];

    public function reviews(){
        return $this->belongsToMany(Product::class,'product_reviews')->withPivot('review','comment')->withTimestamps();
    }
    public function wishList(){
        return $this->belongsToMany(Product::class,'wish_list');
    }
    public function orders(){
        return $this->hasMany(Order::class,'user_id','id');

    }

    public function getImageAttribute($value){
        return $value ? asset('images/users/'.$value):'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }
}
