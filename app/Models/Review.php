<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table='product_reviews';
    protected $fillable=[
        'product_id','vendor_id','comment','review','created_at','updated_at'
    ];
}
