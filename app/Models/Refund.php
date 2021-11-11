<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;


    protected $fillable=['order_id','vendor_id','total_refund_amount','size_id','quantity','price','subtotal_refund_amount','taxes','size','refund_status'];

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function vendor()
    {
        return $this->belongsTo(User::class,'vendor_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
