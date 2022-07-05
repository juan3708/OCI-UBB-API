<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactanosOci extends Mailable
{
    use Queueable, SerializesModels;
    
    public $content;
    public $subject;
    public $emailCC;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $name, $content, $emailCC)
    {
        //
        $this -> content = $content;
        $this -> subject = $subject;
        $this -> emailCC =$emailCC;
        $this -> name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contactanos');
    }
}
