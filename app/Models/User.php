<?php

namespace App\Models;

use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable,SoftDeletes;


    protected $fillable = [
        'name',
        'geoLocation',
        'store_name',
        'email',
        'password',
        'add_product',
        'add_session',
        'image',
        'activation',
        'phone',
        'whatsapp',
        'location',
        'opening_time',
        'closing_time',
        'code',
        'session_rooms_limitation_indoor',
        'session_rooms_limitation_outdoor',
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];


    public function products(){
        return $this->hasMany(Product::class);
    }
    public function sessions(){
        return $this->hasMany(Xsession::class);
    }
    public function getImageAttribute($value){
        return $value ? asset('images/users/'.$value):'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function products_sizes(){
        return $this->hasManyThrough(Size::class,Product::class,'user_id','product_id');
    }

    public function orders_sizes(){
        return $this->products_sizes()->has('order');
    }

    public function orders(){
        return $this->belongsToMany(Order::class,'order_vendor','vendor_id','order_id')->withPivot(['subtotal','taxes','total_amount']);
    }

    public function myActivities(){
        return $this->hasMany(Activity::class,'vendor_id');
    }
    public function refunds(){
        return $this->hasMany(Refund::class,'vendor_id');
    }
    public function reservations(){
        return $this->hasMany(Reservation::class,'vendor_id','id');
    }
    public function refund_groups(){
        return $this->hasMany(RefundGroup::class,'vendor_id');
    }
    public function activitesBelongsToMe(){
        return $this->hasMany(Activity::class,'belongs_to_id');
    }


    public function codes()
    {
        return $this->hasMany(Promocode::class);
    }

}
