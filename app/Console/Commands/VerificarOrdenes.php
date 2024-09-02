<?php

namespace App\Console\Commands;

use App\Jobs\EnviarNotificacionRestaurante;
use App\Models\Informacion;
use App\Models\Ordenes;
use App\Models\Registros;
use App\Models\Servicios;
use App\Models\UsuariosServicios;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

        $infor = Informacion::where('id', 1)->first();

        if($infor->crono_activo == 1){

            $fecha = Carbon::now('America/El_Salvador');

            $registro = new Registros();
            $registro->fecha = $fecha;
            $registro->save();
        }


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

            $titulo = "Hay Nuevas Ordenes";
            $mensaje = "Revisar las ordenes Pendientes";

            Log::info('enviado desde Timer');

            dispatch(new EnviarNotificacionRestaurante($pilaTokenRestaurante, $titulo, $mensaje));
        }


    }
}
