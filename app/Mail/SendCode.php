<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\Translation\t;

class SendCode extends Mailable
{
    use Queueable, SerializesModels;
    public $code;
    public $name;

    public function __construct($code,$name)
    {
        $this->code=$code;
        $this->name=$name;
    }


    public function build()
    {
        return $this->markdown('emails.send_otp',['code'=>$this->code,'name' => $this->name]);
    }
}
