<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactanosOciResp extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        
        $this->name = $name;
        $this->subject = 'Gracias por contactarnos';
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.respuestaContactanos');
    }
}
