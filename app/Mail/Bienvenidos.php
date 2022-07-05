<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Bienvenidos extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fecha;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $fecha)
    {
        $this->nombre = $nombre;
        $this->fecha = $fecha;
        $this->subject = "Bienvenido a las OCI - UBB!";
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bienvenidos');
    }
}
