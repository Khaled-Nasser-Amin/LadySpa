<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCard extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $order,$vendor;

    public function __construct($order,$vendor)
    {
        $this->order=$order;
        $this->vendor=$vendor;
    }


    public function build()
    {
       return $this->markdown('emails.order_card',['vendor' => $this->vendor,'order'=> $this->order]);
    }
}
