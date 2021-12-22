<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmptyStockSize extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $product_name,$vendor_name,$size;

    public function __construct($product_name,$vendor_name,$size)
    {
        $this->product_name=$product_name;
        $this->vendor_name=$vendor_name;
        $this->size=$size;
    }


    public function build()
    {
       return $this->markdown('emails.empty_stock',['product_name' => $this->product_name,'vendor_name'=> $this->vendor_name,'size'=> $this->size]);
    }
}
