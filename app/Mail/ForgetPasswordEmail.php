<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\Translation\t;

class ForgetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $url;
    public $name;

    public function __construct($url,$name)
    {
        $this->url=$url;
        $this->name=$name;
    }


    public function build()
    {
        return $this->markdown('emails.forget_password',['url'=>$this->url,'name' =>$this->name]);
    }
}
