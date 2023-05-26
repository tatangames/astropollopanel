<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Servicios;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OneSignal;

class NotificacionesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }


    public function indexNotificacionPorRestaurante(){

        $restaurantes = Servicios::orderBy('nombre')->get();

        return view('backend.admin.notificaciones.restaurantes.vistanotiporrestaurante', compact('restaurantes'));
    }


    public function enviarNotificacionPorServicio(Request $request){


        $rules = array(
            'idservicio' => 'required',
            'titulo' => 'required',
            'mensaje' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }


        // OBTENER TODAS LAS ZONAS ASIGNADAS AL RESTAURANTE

        $arrayZonas = ZonasServicio::where('id_servicios', $request->idservicio)->get();

        $pilaIdZonas = array();

        foreach ($arrayZonas as $info){
            array_push($pilaIdZonas, $info->id_zonas);
        }

        $arrayDirecciones = DireccionCliente::whereIn('id_zonas', $pilaIdZonas)
            ->where('seleccionado', 1)
            ->get();

        $pilaTokenCliente = array();

        foreach ($arrayDirecciones as $data){

            $infoCliente = Clientes::where('id', $data->id_cliente)->first();

            if($infoCliente->token_fcm != null){
                array_push($pilaTokenCliente, $infoCliente->token_fcm);
            }
        }

        if($pilaTokenCliente != null){

            $tituloNoti = $request->titulo;
            $mensajeNoti = $request->mensaje;


            $AppId = config('googleapi.IdApp_Cliente');

            $AppGrupoNotiPasivo = config('googleapi.IdGrupoPasivoCliente');


            $tokenUsuario = $infoCliente->token_fcm;

            $contents = array(
                "en" => $mensajeNoti
            );

            $params = array(
                'app_id' => $AppId,
                'contents' => $contents,
                'android_channel_id' => $AppGrupoNotiPasivo,
                'include_player_ids' => is_array($tokenUsuario) ? $tokenUsuario : array($tokenUsuario)
            );

            $params['headings'] = array(
                "en" => $tituloNoti
            );

            OneSignal::sendNotificationCustom($params);
        }


        return ['success' => 1];
    }


    public function indexListaDireccioneNotificacion(){
        return view('backend.admin.notificaciones.clientes.vistanotiporcliente');
    }


    public function tablaClientesDireccionesNotificacion(){

        $lista = DireccionCliente::orderBy('telefono')->get();

        return view('backend.admin.notificaciones.clientes.tablanotiporcliente', compact('lista'));
    }


    public function informacionCliente(Request $request){


        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionCliente::where('id', $request->id)->first()){

            $infoCliente = Clientes::where('id', $info->id_cliente)->first();

            if($infoCliente->token_fcm != null){
                return ['success' => 1, 'info' => $info];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 99];
        }
    }


    public function enviarNotiPorCliente(Request $request){


        $regla = array(
            'id' => 'required', // id de direccion
            'titulo' => 'required',
            'mensaje' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionCliente::where('id', $request->id)->first()){

            $infoCliente = Clientes::where('id', $info->id_cliente)->first();

            if($infoCliente->token_fcm != null){

                // ENVIAR NOTIFICACION


                $tituloNoti = $request->titulo;
                $mensajeNoti = $request->mensaje;


                $AppId = config('googleapi.IdApp_Cliente');

                $AppGrupoNotiPasivo = config('googleapi.IdGrupoPasivoCliente');

                $tokenUsuario = $infoCliente->token_fcm;

                $contents = array(
                    "en" => $mensajeNoti
                );

                $params = array(
                    'app_id' => $AppId,
                    'contents' => $contents,
                    'android_channel_id' => $AppGrupoNotiPasivo,
                    'include_player_ids' => is_array($tokenUsuario) ? $tokenUsuario : array($tokenUsuario)
                );

                $params['headings'] = array(
                    "en" => $tituloNoti
                );

                OneSignal::sendNotificationCustom($params);

                return ['success' => 1];

            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 99];
        }
    }





}
