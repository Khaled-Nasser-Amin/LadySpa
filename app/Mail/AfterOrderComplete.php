<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AfterOrderComplete extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $message,$store_name;

    public function __construct($message,$store_name)
    {
        $this->message=$message;
        $this->store_name=$store_name;

    }


    public function build()
    {
       return $this->markdown('emails.after_order_complete',['message' => $this->message,'store_name' => $this->store_name]);
    }
}
