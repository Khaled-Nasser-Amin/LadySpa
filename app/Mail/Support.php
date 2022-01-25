<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\Translation\t;

class Support extends Mailable
{
    use Queueable, SerializesModels;
    public $message,$email;

    public function __construct($email,$message)
    {
        $this->message=$message;
        $this->email=$email;
    }


    public function build()
    {
        return $this->from($this->email)->markdown('admin.emails.support',['message'=>$this->message]);
    }
}
