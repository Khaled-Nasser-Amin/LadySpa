<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable=['image','name','expire_at'];
    public function getImageAttribute($value){
        return asset('images/banners/'.$value);
    }
}
