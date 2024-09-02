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

class EnviarNotificacionMotorista implements ShouldQueue
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
            'app_id' => 'ef1c8fd5-494d-47e7-abac-fbbee5c24188',
            'contents' => $contents,
            'include_player_ids' => is_array($this->arrayOnesignal) ? $this->arrayOnesignal : array($this->arrayOnesignal),
            'android_channel_id' => 'ddeed491-0e02-42a6-8fdd-95736c067eee'
        );

        $params['headings'] = array(
            "en" => $tituloNoti
        );

        try {
            OneSignal::sendNotificationCustom($params);
        } catch (\Exception $e) {
            Log::info("Error al enviar la notificación para Motorista");
        }
    }
}
