<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundGroup extends Model
{
    use HasFactory;


    protected $fillable=['order_id','vendor_id','total_refund_amount','group_id','quantity','price','subtotal_refund_amount','taxes','refund_status'];

    public function product()
    {
        return $this->belongsTo(Product::class)->where('type','group');
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
