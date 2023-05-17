<?php

namespace App\Http\Controllers\Backend\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Ordenes;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdenesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }


    public function indexOrdenesPendientes(){

        return view('backend.admin.ordenes.pendientes.vistaordenespendientes');
    }

    public function tablaOrdenesPendientes(){

        // TODAS LAS ORDENES QUE NO ESTEN CANCELADAS Y NO INICIADAS

        $ordenes = Ordenes::where('estado_iniciada', 0)
            ->where('estado_cancelada', 0)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($ordenes as $info){

            $clienteDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();
            $info->cliente = $clienteDireccion->nombre;
            $info->direccion = $clienteDireccion->direccion;

            $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
            $info->total_orden = number_format((float)$info->total_orden, 2, '.', ',');

            $infoServicio = Servicios::where('id', $info->id_servicio)->first();
            $info->restaurante = $infoServicio->nombre;

            if($info->id_cupones != null){
                // SI UTILIZA CUPON
                $info->sicupon = 1;
            }else{
                $info->sicupon = 0;
            }
        }

        return view('backend.admin.ordenes.pendientes.tablaordenespendientes', compact('ordenes'));
    }


    public function informacionClienteOrden(Request $request){

        $rules = array(
            'id' => 'required', // id orden
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Ordenes::where('id', $request->id)->first()){

            $infoDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

            return ['success' => 1, 'cliente' => $infoDireccion];
        }else{
            return ['success' => 99];
        }
    }


    public function mapaDireccionRegistrado($id){

        $googleapi = config('googleapi.Google_API');

        $poligono = OrdenesDirecciones::where('id_ordenes', $id)->first();

        $latitud = $poligono->latitud;
        $longitud = $poligono->longitud;

        return view('backend.admin.clientes.direcciones.mapa.maparegistrado', compact('latitud', 'longitud', 'googleapi'));
    }



    //********************************************************


    public function indexOrdenesIniciadasHoy(){

        $dataFecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y", strtotime($dataFecha));

        return view('backend.admin.ordenes.iniciadahoy.vistaordenesiniciadashoy', compact('fecha'));
    }


    public function tablaOrdenesIniciadasHoy(){


        $fecha = Carbon::now('America/El_Salvador');
        $ordenes = Ordenes::whereDate('fecha_orden', $fecha)
            ->where('estado_iniciada', 1)
            ->where('estado_cancelada', 0)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($ordenes as $info){

            $clienteDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();
            $info->cliente = $clienteDireccion->nombre;
            $info->direccion = $clienteDireccion->direccion;

            $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
            $info->total_orden = number_format((float)$info->total_orden, 2, '.', ',');

            $infoServicio = Servicios::where('id', $info->id_servicio)->first();
            $info->restaurante = $infoServicio->nombre;

            if($info->id_cupones != null){
                // SI UTILIZA CUPON
                $info->sicupon = 1;
            }else{
                $info->sicupon = 0;
            }



            // FECHA DE ORDEN INICIADA
            $info->fecha_iniciada = date("h:i A d-m-Y", strtotime($info->fecha_iniciada));


            // FECHA DE ENTREGA ESTIMADA + EL TIEMPO EXTRA DE LA ZONA

            $fechaInicioPreparar = Carbon::parse($info->fecha_iniciada);

            $horaEstimada = $fechaInicioPreparar->addMinute($info->tiempo_estimada)->format('h:i A d-m-Y');
            $info->horaEstimadaEntrega = $horaEstimada;
        }

        return view('backend.admin.ordenes.iniciadahoy.tablaordenesiniciadashoy', compact('ordenes'));
    }


    public function informacionProcesoOrdenIniciadas(Request $request){


        $rules = array(
            'id' => 'required', // id orden
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->id)->first()){

            // FORMATEOS

            $arrayOrdenes = Ordenes::where('id', $request->id)->get();

            foreach ($arrayOrdenes as $info){

                $infoServicio = Servicios::where('id', $info->id_servicio)->first();
                $info->restaurante = $infoServicio->nombre;

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
                $info->total_orden = '$' . number_format((float)$info->total_orden, 2, '.', ',');
                if($info->total_cupon != null){
                    $info->total_cupon = '$' . number_format((float)$info->total_cupon, 2, '.', ',');
                }

                $haycupon = "";
                if($info->id_cupones != null){
                    $haycupon = "Si";
                }

                $info->haycupon = $haycupon;

                // PROCESOS

                $fechaInicioPreparar = Carbon::parse($info->fecha_iniciada);

                $info->fecha_iniciada = date("h:i A d-m-Y", strtotime($info->fecha_iniciada));

                $info->fechaEstimadaEntrega = $fechaInicioPreparar->addMinute($info->tiempo_estimada)->format('h:i A d-m-Y');


                if($info->estado_preparada == 1){
                    $info->fecha_preparada = date("h:i A d-m-Y", strtotime($info->fecha_preparada));
                }


                if($info->estado_camino == 1){
                    $info->fecha_camino = date("h:i A d-m-Y", strtotime($info->fecha_camino));
                }


                if($info->estado_entregada == 1){
                    $info->fecha_entregada = date("h:i A d-m-Y", strtotime($info->fecha_entregada));
                }
            }


            return ['success' => 1, 'info' => $arrayOrdenes];
        }else{
            return ['success' => 99];
        }
    }




    // *********************** ORDENES CANCELADAS HOY  *********************************

    public function indexOrdenesCanceladasHoy(){

        $dataFecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y", strtotime($dataFecha));

        return view('backend.admin.ordenes.canceladas.vistaordenescanceladashoy', compact('fecha'));
    }


    public function tablaOrdenesCanceladasHoy(){

        $fecha = Carbon::now('America/El_Salvador');
        $ordenes = Ordenes::whereDate('fecha_orden', $fecha)
            ->where('estado_cancelada', 1)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($ordenes as $info){

            $clienteDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();
            $info->cliente = $clienteDireccion->nombre;
            $info->direccion = $clienteDireccion->direccion;

            $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));
            $info->total_orden = number_format((float)$info->total_orden, 2, '.', ',');

            $infoServicio = Servicios::where('id', $info->id_servicio)->first();
            $info->restaurante = $infoServicio->nombre;

            if($info->id_cupones != null){
                // SI UTILIZA CUPON
                $info->sicupon = 1;
            }else{
                $info->sicupon = 0;
            }


            if($info->cancelado_por == 1){
                $info->canceladopor = "Cliente";
            }else{
                $info->canceladopor = "Restaurante";
            }

            $info->fecha_cancelada = date("h:i A d-m-Y", strtotime($info->fecha_cancelada));

            // FECHA DE ORDEN INICIADA
            $info->fecha_iniciada = date("h:i A d-m-Y", strtotime($info->fecha_iniciada));


            // FECHA DE ENTREGA ESTIMADA + EL TIEMPO EXTRA DE LA ZONA

            $fechaInicioPreparar = Carbon::parse($info->fecha_iniciada);

            $horaEstimada = $fechaInicioPreparar->addMinute($info->tiempo_estimada)->format('h:i A d-m-Y');
            $info->horaEstimadaEntrega = $horaEstimada;
        }


        return view('backend.admin.ordenes.canceladas.tablaordenescanceladashoy', compact('ordenes'));
    }


}
