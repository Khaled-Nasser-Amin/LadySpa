<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function vendor()
    {
        return $this->belongsTo(User::class);
    }

    public function used_customers(){
        return $this->belongsToMany(Promocode::class,'used_codes','customer_id','code_id');
    }
}
