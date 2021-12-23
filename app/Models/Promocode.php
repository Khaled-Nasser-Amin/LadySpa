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
        return $this->belongsToMany(Customer::class,'used_codes','code_id','customer_id');
    }

    public function spcialCustomers()
    {
        return $this->hasMany(Customer::class,'special_code_id');
    }

}
