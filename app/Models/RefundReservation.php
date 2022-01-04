<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundReservation extends Model
{
    use HasFactory;

    protected $fillable=['reservation_id','vendor_id','number_of_persons','customer_id','total_refund_amount','session_id','number_of_additions','subtotal_refund_amount','taxes','refund_status'];

    public function session()
    {
        return $this->belongsTo(Xsession::class);
    }
    public function vendor()
    {
        return $this->belongsTo(User::class,'vendor_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function reservation()
    {
        return $this->belongsTo(Reservation::class)->where('payment_way','online payment');
    }
}
