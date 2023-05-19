<?php

namespace App\Http\Controllers\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesDirecciones;
use App\Models\OrdenesMotoristas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdenesController extends Controller
{

    public function verListadoOrdenesActivasCliente(Request $request)
    {


        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if (Clientes::where('id', $request->clienteid)->first()) {

            // solo ordenes no canceladas, ni completadas
            $orden = Ordenes::where('id_cliente', $request->clienteid)
                ->where('visible', 1)
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($orden as $info) {
                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

                $infoDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->direccion = $infoDireccion->direccion;


                // CUPONES
                $haycupon = 0;
                $totalAPagar = $info->total_orden;

                if ($info->id_cupones != null) {
                    $haycupon = 1;

                    // SI ESTO NO ESTA NULL, SIGNIFICA QUE SE APLICO CUPON DINERO O PORCENTAJE

                    if ($info->total_cupon != null) {

                        $totalAPagar = $info->total_cupon;

                    }

                }


                $info->totalformat = '$' . number_format((float)$totalAPagar, 2, '.', ',');

                $estado = "Orden Pendiente";

                // LOS ESTADOS VAN POR PRIORIDAD

                if ($info->estado_iniciada == 1) {
                    $estado = "Orden Iniciada";
                }

                if ($info->estado_camino == 1) {
                    $estado = "Orden en Camino";
                }

                if ($info->estado_entregada == 1) {
                    $estado = "Orden Entregada";
                }

                // Si fue cancelada

                if ($info->estado_cancelada == 1) {
                    $estado = "Orden Cancelada";
                }


                $info->haycupon = $haycupon;
                $info->estado = $estado;
            }

            return ['success' => 1, 'ordenes' => $orden];
        } else {
            return ['success' => 2];
        }

    }


    public function informacionOrdenIndividual(Request $request)
    {


        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if (Ordenes::where('id', $request->ordenid)->first()) {

            $orden = Ordenes::where('id', $request->ordenid)->get();

            foreach ($orden as $info) {

                if ($info->estado_iniciada == 1) { // propietario inicia la orden

                    // cuando el restaurante manda a iniciar la orden se toma su tiempo y el de la zona
                    // esto dara el campo fecha ya formateado

                    $fechaEstimada = "Hora Estimada de Entrega \n" . date("h:i A d-m-Y", strtotime($info->fecha_estimada));
                    $textoIniciada = "Orden Iniciada";
                } else {
                    $textoIniciada = "Orden Pendiente";
                    $fechaEstimada = "";
                }

                $info->textoiniciada = $textoIniciada;
                $info->fechaestimada = $fechaEstimada;


                //****************************************************


                if ($info->estado_camino == 1) {

                    $textoCamino = "Motorista en Camino";
                    $fechaCamino = date("h:i A d-m-Y", strtotime($info->fecha_camino));
                } else {

                    $textoCamino = "";
                    $fechaCamino = "";
                }

                $info->textocamino = $textoCamino;
                $info->fechacamino = $fechaCamino;


                if ($info->estado_cancelada == 1) { // motorista inicia la entrega
                    $info->fecha_cancelada = date("h:i A d-m-Y", strtotime($info->fecha_cancelada));
                }


                $info->nota_cancelada = "Nota: " . $info->nota_cancelada;



                //**************************


                if ($info->estado_entregada == 1) {

                    $textoEntregada = "Orden Entregada";
                    $fechaEntregada = date("h:i A d-m-Y", strtotime($info->fecha_camino));
                } else {

                    $textoEntregada = "";
                    $fechaEntregada = "";
                }

                $info->textoentregada = $textoEntregada;
                $info->fechaentregada = $fechaEntregada;

            }

            return ['success' => 1, 'ordenes' => $orden];
        } else {
            return ['success' => 2];
        }

    }


    public function verMotoristaOrden(Request $request)
    {

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if ($infoMotoOrden = OrdenesMotoristas::where('id_ordenes', $request->ordenid)->first()) {

            $infoMotorista = MotoristasServicios::where('id', $infoMotoOrden->id_motorista)->first();

            $foto = $infoMotorista->imagen;
            $nombre = $infoMotorista->nombre;
            $vehiculo = $infoMotorista->vehiculo;
            $placa = $infoMotorista->placa;

            return ['success' => 1, 'foto' => $foto, 'nombre' => $nombre, 'vehiculo' => $vehiculo, 'placa' => $placa];
        } else {
            // motorista no encontrado
            return ['success' => 2];
        }
    }



    public function calificarLaOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required',
            'valor' => 'required'
        );

        /// mensaje

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(OrdenesMotoristas::where('id_ordenes', $request->ordenid)->first()){

            OrdenesMotoristas::where('id_ordenes', $request->ordenid)
                ->update(['experiencia' => $request->valor,
                    'mensaje' => $request->mensaje]);


            // QUITAR VISIBILIDAD A LAS LISTAS DE ORDENES AL CLIENTE


            Ordenes::where('id', $request->ordenid)
                ->update(['visible' => 0]);


            return ['success' => 1];
        }else{

            // no se podra calificar
            return ['success' => 2];
        }
    }

}




