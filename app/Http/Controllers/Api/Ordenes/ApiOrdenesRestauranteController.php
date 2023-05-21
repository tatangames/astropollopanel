<?php

namespace App\Http\Controllers\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\Cupones;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Productos;
use App\Models\UsuariosServicios;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiOrdenesRestauranteController extends Controller
{
    public function nuevasOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = UsuariosServicios::where('id', $request->id)->first()){

            // USUARIO BLOQUEADO
            if($infoUsuario->bloqueado == 1){
                return ['success'=> 1];
            }

            $arrayOrdenes = Ordenes::where('estado_iniciada', 0)
                ->where('estado_cancelada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'ASC')
                ->get();

            $conteoOrdenes = Ordenes::where('estado_iniciada', 0)
                ->where('estado_cancelada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->count();


            foreach($arrayOrdenes as $info){

                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

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


                $info->haycupon = $haycupon;
                $info->cliente = $infoOrdenesDireccion->nombre;
                $info->direccion = $infoOrdenesDireccion->direccion;
                $info->telefono = $infoOrdenesDireccion->telefono;
            }

            return ['success' => 2, 'hayordenes' => $conteoOrdenes, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 3];
        }
    }



    public function listadoProductosOrden(Request $request){


        $reglaDatos = array(
            'idorden' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar la orden
        if($infoOrden = Ordenes::where('id', $request->idorden)->first()){

            $lista = OrdenesDescripcion::where('id_ordenes', $infoOrden->id)->get();

            foreach ($lista as $info){

                $infoProducto = Productos::where('id', $info->id_producto)->first();
                $info->nombreproducto = $infoProducto->nombre;

                $info->utiliza_imagen = $infoProducto->utiliza_imagen;
                $info->imagen = $infoProducto->imagen;

                $info->descripcion = $infoProducto->descripcion;

                $multi = $info->cantidad * $info->precio;
                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');

                $info->precio = '$' . number_format((float)$info->precio, 2, '.', ',');
            }


            $minutos = 10;


            return ['success' => 1, 'productos' => $lista, 'minutos' => $minutos];
        }
        else{

            return ['success' => 2];
        }
    }



    public function infoProductoOrdenadoIndividual(Request $request){


        $reglaDatos = array(
            'idordendescrip' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoOrdenDescr = OrdenesDescripcion::where('id', $request->idordendescrip)->first()){

            $lista = OrdenesDescripcion::where('id', $infoOrdenDescr->id)->get();

            foreach ($lista as $info){

                $infoProducto = Productos::where('id', $info->id_producto)->first();
                $info->nombreproducto = $infoProducto->nombre;

                $info->utiliza_imagen = $infoProducto->utiliza_imagen;
                $info->imagen = $infoProducto->imagen;

                $info->descripcion = $infoProducto->descripcion;

                $multi = $info->cantidad * $info->precio;
                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');

                $info->precio = '$' . number_format((float)$info->precio, 2, '.', ',');
            }

            return ['success' => 1, 'productos' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function iniciarOrdenPorRestaurante(Request $request){

        $reglaDatos = array(
            'idorden' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoOrden = Ordenes::where('id', $request->idorden)->first()){

            // ORDEN ESTA CANCELADA
            if($infoOrden->estado_cancelada == 1){
                $titulo = "Orden Cancelada";
                $mensaje = "Fue cancelada por el Cliente";
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // INICIO DE ORDEN
            if($infoOrden->estado_iniciada == 0){

                $fecha = Carbon::now('America/El_Salvador');

                // TIEMPO DE ZONA + DEL RESTAURANTE
                $infoZona = Zonas::where('id', $infoOrden->id_zona)->first();

                $fechaInicioPreparar = Carbon::parse($fecha);
                $horaEstimada = $fechaInicioPreparar->addMinute($infoZona->tiempo_extra);



                //****************
                $fechaHoy = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $infoOrden->id)
                    ->update(['estado_iniciada' => 1,
                              'fecha_iniciada' => $fechaHoy,
                              'fecha_estimada' => $horaEstimada]);


                // NOTIFICACION ONE SIGNAL

                $infoCliente = Clientes::where('id', $infoOrden->id_cliente)->first();

                if($infoCliente->token_fcm != null){

                    //$tituloNoti = "Orden #" . $request->ordenid . " Aceptada";
                    //$mensajeNoti = "Su orden inicia su Preparación";

                    // ENVIAR NOTIFICACIONES


                }

                // NOTIFICACION ONE SIGNAL QUE HAY ORDENES QUE PUEDEN SER AGARRADAS YA
                $arrayMotoristas = MotoristasServicios::where('id_servicios', $infoOrden->id_servicio)
                    ->where('activo', 1)
                    ->where('notificacion', 1)
                    ->get();

                $pilaMotoristas = array();

                foreach($arrayMotoristas as $p){
                    if($p->token_fcm != null){
                        array_push($pilaMotoristas, $p->token_fcm);
                    }
                }


                if($pilaMotoristas != null) {
                    $tituloNoti = "Hay Nuevas Ordenes";
                    $mensajeNoti = "Por Favor Verificar";
                    // ENVIAR NOTIFICACIONES



                }

                $titulo = "Nota";
                $mensaje = "Orden Iniciada";

                // orden iniciada
                return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            $titulo = "Nota";
            $mensaje = "Orden Iniciada";

            // orden iniciada por defecto
            return ['success'=> 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }else{
            return ['success'=> 3];
        }

    }



    public function cancelarOrden(Request $request){

        $reglaDatos = array(
            'idorden' => 'required',
            'mensaje' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoOrden = Ordenes::where('id', $request->idorden)->first()){

            DB::beginTransaction();

            try {

                // VERIFICAR QUE ORDEN NO ESTE CANCELADA
                if($infoOrden->estado_cancelada == 1){
                    $titulo = "Nota";
                    if($infoOrden->cancelado_por == 1){
                        $mensaje = "La orden ya habia sido cancelada por el Cliente";
                    }
                    else{
                        $mensaje = "Orden fue cancelada";
                    }


                    return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                }

                // LA ORDEN PUEDE CANCELARSE

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $infoOrden->id)->update(['estado_cancelada' => 1,
                    'fecha_cancelada' => $fecha,
                    'cancelado_por' => 2,
                    'nota_cancelada' => $request->mensaje]);


                // SI SE UTILIZO CUPON SE DEBE DE VOLVER A SUMAR SU CONTADOR EN - 1

                if($infoOrden->id_cupones_copia != null){
                    // si hay 1 cupon

                    $infoCupones = Cupones::where('id', $infoOrden->id_cupones_copia)->first();

                    $contador = $infoCupones->contador - 1;

                    Cupones::where('id', $infoCupones->id)->update(['contador' => $contador]);
                }

                    // notificacion a cliente que su orden fue cancelada
                    $infoCliente = Clientes::where('id', $infoOrden->id_cliente)->first();

                    if($infoCliente->token_fcm != null){

                        $tituloNoti = "Orden #" . $request->ordenid . " Cancelada";
                        $mensajeNoti = "Revise su Orden";

                        // ENVIAR NOTIFICACION
                    }

                    DB::commit();

                    // Orden cancelada por restaurante

                    $titulo = "Nota";
                    $mensaje = "Orden cancelada correctamente";

                    return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];


            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 99];
            }
        }else{

            // orden no encontrada
            return ['success' => 99];
        }
    }





    public function preparacionOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = UsuariosServicios::where('id', $request->id)->first()){

            $arrayOrdenes = Ordenes::where('estado_iniciada', 1)
                ->where('estado_cancelada', 0)
                ->where('estado_preparada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'ASC')
                ->get();

            $conteoOrdenes = Ordenes::where('estado_iniciada', 1)
                ->where('estado_cancelada', 0)
                ->where('estado_preparada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->count();

            foreach($arrayOrdenes as $info){

                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

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

                $info->haycupon = $haycupon;
                $info->cliente = $infoOrdenesDireccion->nombre;
                $info->direccion = $infoOrdenesDireccion->direccion;
                $info->telefono = $infoOrdenesDireccion->telefono;
            }

            return ['success' => 1, 'hayordenes' => $conteoOrdenes, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 3];
        }

    }






}
