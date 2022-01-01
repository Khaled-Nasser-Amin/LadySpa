<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationTime extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function reservation(){
        return $this->belongsTo(Reservation::class);

    }

    public function vendor(){
        return $this->belongsTo(User::class,'vendor_id');

    }
}
