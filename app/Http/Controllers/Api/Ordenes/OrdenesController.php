<?php

namespace App\Http\Controllers\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Ordenes;
use App\Models\OrdenesDirecciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdenesController extends Controller
{

    public function verListadoOrdenesActivasCliente(Request $request){


        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->clienteid)->first()){

            // solo ordenes no canceladas, ni completadas
            $orden = Ordenes::where('id_cliente', $request->clienteid)
                ->where('visible', 1)
                ->orderBy('id', 'DESC')
                ->get();

            foreach($orden as $info){
                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

                $infoDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->direccion = $infoDireccion->direccion;



                // CUPONES
                $haycupon = 0;
                $totalAPagar = $info->total_orden;

                if($info->id_cupones != null){
                    $haycupon = 1;

                    // SI ESTO NO ESTA NULL, SIGNIFICA QUE SE APLICO CUPON DINERO O PORCENTAJE

                    if($info->total_cupon != null){

                        $totalAPagar = $info->total_cupon;

                    }

                }


                $info->totalformat = '$' . number_format((float)$totalAPagar, 2, '.', ',');

                $estado = "Orden Pendiente";

                // LOS ESTADOS VAN POR PRIORIDAD

                if($info->estado_iniciada == 1){
                    $estado = "Orden Iniciada";
                }

                if($info->estado_camino == 1){
                    $estado = "Orden en Camino";
                }

                if($info->estado_entregada == 1){
                    $estado = "Orden Entregada";
                }

                // Si fue cancelada

                if($info->estado_cancelada == 1){
                    $estado = "Orden Cancelada";
                }



                $info->haycupon = $haycupon;
                $info->estado = $estado;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }

    }




    public function informacionOrdenIndividual(Request $request){


        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            foreach($orden as $info){

                if($info->estado_iniciada == 1){ // propietario inicia la orden
                    $info->fecha_iniciada = date("h:i A d-m-Y", strtotime($info->fecha_iniciada));
                }

                if($info->estado_cancelada == 1){ // motorista inicia la entrega
                    $info->fecha_cancelada = date("h:i A d-m-Y", strtotime($info->fecha_cancelada));
                }

            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }



    }



}
