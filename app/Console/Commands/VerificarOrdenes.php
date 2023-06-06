<?php

namespace App\Console\Commands;

use App\Models\Ordenes;
use App\Models\Servicios;
use App\Models\UsuariosServicios;
use Illuminate\Console\Command;
use Carbon\Carbon;
use OneSignal;


class VerificarOrdenes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ordenes:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notificacion cada 1 minuto a restaurante';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // ENVIO DE NOTIFICACION A RESTAURANTE CADA 1 MINUTO SI LA ORDEN
        // NO HA SIDO INICIADA O CANCELADA


        $arrayOrdenHoy = Ordenes::where('estado_iniciada', 0)
            ->where('estado_cancelada', 0)
            ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
            ->get();

        $pilaTokenRestaurante = array();

        foreach ($arrayOrdenHoy as $info){
            // obtener token del servicio que esta asociado esta orden

            // EL PRIMER USUARIO QUE NO ESTE BLOQUEADO
            if($infoUsuario = UsuariosServicios::where('id_servicios', $info->id_servicio)
                ->where('bloqueado', 0)
                ->first()){
                array_push($pilaTokenRestaurante, $infoUsuario->token_fcm);
            }
        }

        if($pilaTokenRestaurante != null) {

            $AppId = config('googleapi.IdApp_Restaurante');

            $AppGrupoNotiPasivo = config('googleapi.IdGrupoAlarmaRestaurante');

            $mensaje = "Hay Nuevas Ordenes";
            $titulo = "Revisar las ordenes Pendientes";


            $contents = array(
                "en" => $mensaje
            );

            $params = array(
                'app_id' => $AppId,
                'contents' => $contents,
                'android_channel_id' => $AppGrupoNotiPasivo,
                'include_player_ids' => is_array($pilaTokenRestaurante) ? $pilaTokenRestaurante : array($pilaTokenRestaurante)
            );

            $params['headings'] = array(
                "en" => $titulo
            );

            OneSignal::sendNotificationCustom($params);
        }


    }
}
