<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OneSignal;
use Exception;

class EnviarNotificacionUsuario implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $arrayOnesignal;
    protected $titulo;
    protected $descripcion;

    /**
     * Create a new job instance.
     */
    public function __construct($arrayOnesignal, $titulo, $descripcion)
    {
        $this->arrayOnesignal = $arrayOnesignal;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tituloNoti = $this->titulo;
        $mensajeNoti = $this->descripcion;

        $contents = array(
            "en" => $mensajeNoti
        );

        $params = array(
            'app_id' => 'f86a2ee4-a10b-4a86-a063-151be6845bce',
            'contents' => $contents,
            'include_player_ids' => is_array($this->arrayOnesignal) ? $this->arrayOnesignal : array($this->arrayOnesignal),
            'android_channel_id' => '59f35031-6bad-4833-b73b-00f384c2be89'
        );

        $params['headings'] = array(
            "en" => $tituloNoti
        );

        try {
            OneSignal::sendNotificationCustom($params);
        } catch (\Exception $e) {
            Log::info("Error al enviar la notificación para Usuarios");
        }
    }
}
