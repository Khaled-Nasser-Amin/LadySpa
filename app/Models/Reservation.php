<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function customer(){
        return $this->belongsTo(Customer::class,'user_id','id');

    }
    public function vendor(){
        return $this->belongsTo(User::class,'vendor_id','id');

    }
    public function session(){
        return $this->belongsTo(Xsession::class,'session_id','id');

    }
    public function additions(){
        return $this->belongsToMany(Addition::class,'reservations_additions','reservation_id','addition_id')->withPivot(['price','name_ar','name_en']);
    }

    public function refunds(){
        return $this->hasMany(Refund::class);

    }

    public function times(){
        return $this->hasMany(ReservationTime::class);

    }
}
