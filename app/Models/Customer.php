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
        return $this->belongsToMany(Product::class,'wish_list')->withPivot(['size_id']);
    }
    public function orders(){
        return $this->hasMany(Order::class,'user_id','id');

    }

    public function getImageAttribute($value){
        return $value ? asset('images/users/'.$value):'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function used_promocodes(){
        return $this->belongsToMany(Promocode::class,'used_codes','customer_id','code_id');
    }

    public function specialCode()
    {
        return $this->belongsTo(Promocode::class,'special_code_id')->where('type_of_code','special');
    }
    public function reservations(){
        return $this->hasMany(Reservation::class,'user_id','id');

    }
}
