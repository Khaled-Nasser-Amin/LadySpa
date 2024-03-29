<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded=['id'];

    public function products(){
        return $this->belongsToMany(Product::class,'products_taxes');
    }
    public function sessions(){
        return $this->belongsToMany(Xsession::class,'products_taxes','tax_id','session_id');
    }
}
