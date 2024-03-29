<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationTransaction extends Model
{
    use HasFactory;

    use HasFactory;
    protected $guarded=[];

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }
}
