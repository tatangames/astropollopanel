<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * UTILIZADO PARA APLICACION MOVIL
     *
     * @return void
     */

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->view('correos.vistarecuperarpassword')
            ->subject('Recuperación de Contraseña - Astro Pollo App')
            ->with($this->data);
    }
}
