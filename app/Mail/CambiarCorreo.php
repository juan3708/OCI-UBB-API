<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambiarCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $newEmail;
    public $fecha;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $newEmail, $fecha)
    {
        $this->nombre = $nombre;
        $this->newEmail = $newEmail;
        $this->fecha = $fecha;
        $this->subject = 'Cambio de correo Sistema OCI - UBB';
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.cambiarCorreo');
    }
}
