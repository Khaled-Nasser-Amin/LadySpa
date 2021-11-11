<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable=['payment_token','twillo_phone','place_limit','twillo_token','twillo_sid','contact_phone','contact_email','contact_whatsapp','contact_land_line','fixed_shipping_cost','shipping_status'];
}
