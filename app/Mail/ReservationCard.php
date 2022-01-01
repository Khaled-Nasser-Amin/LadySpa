<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCard extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $reservation,$name;

    public function __construct($reservation,$name)
    {
        $this->reservation=$reservation;
        $this->name=$name;
    }


    public function build()
    {
       return $this->markdown('emails.reservation_card',['name' => $this->name,'reservation'=> $this->reservation]);
    }
}
