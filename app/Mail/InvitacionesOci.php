<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitacionesOci extends Mailable
{
    use Queueable, SerializesModels;
    public $content;
    public $subject;
    public $start_date;
    public $formLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $start_date, $formLink)
    {
        //
        $this -> content = $content;
        $this -> subject = $subject;
        $this -> start_date = $this -> formatDate($start_date);
        $this -> formLink = $formLink;

    }

    /* Convertir fecha a castellano*/

    private function formatDate($start_date){
        $start_date = substr($start_date, 0, 10);
        $numeroDia = date('d', strtotime($start_date));
        $dia = date('l', strtotime($start_date));
        $mes = date('F', strtotime($start_date));
        $anio = date('Y', strtotime($start_date));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
      $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invitaciones-oci');
    }
}
