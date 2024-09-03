<?php

namespace App\Jobs;

use GuzzleHttp\Client;
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
        $tokenOneSignal = $this->arrayOnesignal;

        try {

            $client = new Client();
            $response = $client->post('https://onesignal.com/api/v1/notifications', [
                'json' => [
                    'app_id' => 'f86a2ee4-a10b-4a86-a063-151be6845bce',
                    'contents' => ['en' => $mensajeNoti],
                    'include_player_ids' => is_array($tokenOneSignal) ? $tokenOneSignal : array($tokenOneSignal),
                    'android_channel_id' => '59f35031-6bad-4833-b73b-00f384c2be89',
                    'headings' => ['en' => $tituloNoti],
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . 'NWJmYWZlYzAtMDMxNy00NTdkLTlhZTYtODY1YjRjNmIyNzZm',
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]);

        } catch (\Exception $e) {
            Log::info("Error al enviar la notificación para Clientes");
        }
    }
}
