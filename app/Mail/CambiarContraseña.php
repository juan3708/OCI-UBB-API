<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambiarContraseña extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $password;
    public $fecha;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $password, $fecha)
    {
        $this->nombre = $nombre;
        $this->password = $password;
        $this->fecha = $fecha;
        $this->subject = 'Nueva contraseña generada Sistema OCI - UBB';
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.cambiarContraseña');
    }
}
