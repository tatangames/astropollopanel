<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CallCenterCliente;
use App\Models\Ordenes;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallCenterOrdenesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexListadoOrdenesHoy(){

        $dataFecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y", strtotime($dataFecha));

        return view('backend.admin.callcenter.ordenes.ordeneshoy.vistacallcenterordeneshoy', compact('fecha'));
    }

    public function tablaListadoOrdenesHoy(){

        // DEBO OBTENER MI ID ASIGNADO DE CLIENTE

        // VALIDACION COMPLETA
        $idSession = Auth::id();

        $infoMicliente = CallCenterCliente::where('id_administrador', $idSession)->first();

        $fecha = Carbon::now('America/El_Salvador');
        $ordenes = Ordenes::whereDate('fecha_orden', $fecha)
            ->where('id_cliente', $infoMicliente->id_cliente)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($ordenes as $info){

            $clienteDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

            $info->cliente = $clienteDireccion->nombre;
            $info->direccion = $clienteDireccion->direccion;
            $info->telefono = $clienteDireccion->telefono;

            $infoServicio = Servicios::where('id', $info->id_servicio)->first();
            $info->restaurante = $infoServicio->nombre;

            $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
            $info->total_orden = '$' . number_format((float)$info->total_orden, 2, '.', ',');


            $estado = "Pendiente";

            if($info->estado_iniciada == 1){
                $estado = "Orden iniciada";
            }


            if($info->estado_preparada == 1){
                $estado = "Orden preparada";
            }

            if($info->estado_camino == 1){
                $estado = "Orden en camino";
            }


            if($info->estado_entregada == 1){
                $estado = "Orden entrega al cliente";
            }

            if($info->estado_cancelada == 1){
                $estado = "Orden Cancelada";
            }

            $info->estadoorden = $estado;
        }


        return view('backend.admin.callcenter.ordenes.ordeneshoy.tablacallcenterordeneshoy', compact('ordenes'));
    }




    public function indexListadoOrdenesTodas(){
        return view('backend.admin.callcenter.ordenes.todasordenes.vistatodasordenescallcenter');
    }

    public function tablaListadoOrdenesTodas(){

        // DEBO OBTENER MI ID ASIGNADO DE CLIENTE

        // VALIDACION COMPLETA
        $idSession = Auth::id();

        $infoMicliente = CallCenterCliente::where('id_administrador', $idSession)->first();

        $fecha = Carbon::now('America/El_Salvador');
        $ordenes = Ordenes::where('id_cliente', $infoMicliente->id_cliente)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($ordenes as $info){

            $clienteDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

            $info->cliente = $clienteDireccion->nombre;
            $info->direccion = $clienteDireccion->direccion;
            $info->telefono = $clienteDireccion->telefono;

            $infoServicio = Servicios::where('id', $info->id_servicio)->first();
            $info->restaurante = $infoServicio->nombre;

            $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
            $info->total_orden = '$' . number_format((float)$info->total_orden, 2, '.', ',');


            $estado = "Pendiente";

            if($info->estado_iniciada == 1){
                $estado = "Orden iniciada";
            }


            if($info->estado_preparada == 1){
                $estado = "Orden preparada";
            }

            if($info->estado_camino == 1){
                $estado = "Orden en camino";
            }


            if($info->estado_entregada == 1){
                $estado = "Orden entrega al cliente";
            }

            if($info->estado_cancelada == 1){
                $estado = "Orden Cancelada";
            }

            $info->estadoorden = $estado;
        }


        return view('backend.admin.callcenter.ordenes.todasordenes.tablatodasordenescallcenter', compact('ordenes'));
    }





}
