<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestSpaceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $space;
    public $emailList;

    public function __construct($event, $space,$emailList)
    {
        $this->event = $event;
        $this->space = $space;
        $this->emailList = $emailList;
    }

    public function build()
    {
        return $this->subject('Solicitud de espacio')
            ->markdown('emails.space_requested')
            ->cc($this->emailList);
    }
}
