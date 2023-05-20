<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoTokenPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * UTILIZADO PARA PANEL DE CONTROL EN LOGIN
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
            ->view('correos.vistatokenpassword')
            ->subject('Recuperación de Contraseña - Astro Pollo App')
            ->with($this->data);
    }
}
