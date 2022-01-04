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


    public function taxes(){
        return $this->belongsToMany(Tax::class,'sessions_taxes','session_id','tax_id');
    }

    public function additions()
    {
        return $this->hasMany(Addition::class,'session_id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class,'session_id');
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
    public function refunds(){
        return $this->hasMany(RefundReservation::class,'session_id');
    }
}
