<?php

namespace App\Http\Controllers\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\OrdenesMotoristas;
use App\Models\Productos;
use App\Models\UsuariosServicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiOrdenesMotoristaController extends Controller
{



    public function nuevasOrdenesMotorista(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = MotoristasServicios::where('id', $request->id)->first()){

            // USUARIO BLOQUEADO
            if($infoUsuario->bloqueado == 1){
                return ['success'=> 1];
            }

            // array de ordenes que han sido agarradas para evitar que se muestren
            $arrayOrdenAsignadas = OrdenesMotoristas::all();

            $pilaIdOrdenes = array();

            foreach ($arrayOrdenAsignadas as $info){
                array_push($pilaIdOrdenes, $info->id_ordenes);
            }


            $arrayOrdenes = Ordenes::where('estado_iniciada', 1)
                ->where('estado_cancelada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->whereNotIn('id', $pilaIdOrdenes)
                ->get();


            $conteoOrdenes = 0;
            foreach($arrayOrdenes as $info){
                $conteoOrdenes++;


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
                $info->referencia = $infoOrdenesDireccion->referencia;
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

            $infoDireccion = OrdenesDirecciones::where('id_ordenes', $infoOrden->id)->first();
            $latitud = $infoDireccion->latitud;
            $longitud = $infoDireccion->longitud;

            return ['success' => 1, 'productos' => $lista, 'latitud' => $latitud, 'longitud' => $longitud];
        }
        else{

            return ['success' => 2];
        }
    }





    public function seleccionarOrden(Request $request){

        $reglaDatos = array(
            'idorden' => 'required',
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoMotorista = MotoristasServicios::where('id', $request->id)->first()){

            if($infoOrden = Ordenes::where('id', $request->idorden)->first()){

                DB::beginTransaction();

                try {

                    // REGLA: SOLO 1 VEZ PUEDE SELECCIONARSE UNA ORDEN

                    if(OrdenesMotoristas::where('id_ordenes', $infoOrden->id)->first()){

                        // ya ha sido seleccionada esta orden
                        $titulo = "No Disponible";
                        $mensaje = "Orden ya fue seleccionada por otro motorista";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                    }

                    // REGLA: VERIFICAR QUE NO ESTE CANCELADA LA ORDEN

                    if($infoOrden->estado_cancelada == 1){

                        $titulo = "No Disponible";
                        $mensaje = "La orden fue Cancelada";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                    }

                    $fecha = Carbon::now('America/El_Salvador');

                    // REGISTRAR LA ORDEN
                    $nueva = new OrdenesMotoristas();
                    $nueva->id_ordenes = $infoOrden->id;
                    $nueva->id_motorista = $infoMotorista->id;
                    $nueva->fecha = $fecha;
                    $nueva->experiencia = null;
                    $nueva->mensaje = null;
                    $nueva->save();

                    DB::commit();

                    $titulo = "Orden Seleccionada";
                    $mensaje = "Puede seguir el Proceso en Ordenes Pendientes";
                    return ['success' => 2,'titulo' => $titulo, 'mensaje' => $mensaje];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 99];
                }

            }else{
                return ['success' => 99]; // orden no encontrada
            }
        }else{
            return ['success' => 99]; // motorista no encontrado
        }

    }




    public function pendientesEntregaOrden(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoMotorista = MotoristasServicios::where('id', $request->id)->first()){

            $arrayOrdenes = DB::table('ordenes_motoristas AS om')
                ->join('ordenes AS o', 'om.id_ordenes', '=', 'o.id')
                ->where('estado_camino', 0)
                ->where('estado_cancelada', 0)
                ->where('om.id_motorista', $infoMotorista->id)
                ->get();

            $conteo = 0;

            // sumar mas envio
            foreach($arrayOrdenes as $info) {
                $conteo++;

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



                $estado = "Pendiente";

                if($info->estado_preparada == 1){
                    $estado = "Orden Lista para Entrega";
                }

                $info->estado = $estado;

                $info->haycupon = $haycupon;
                $info->cliente = $infoOrdenesDireccion->nombre;
                $info->direccion = $infoOrdenesDireccion->direccion;
                $info->telefono = $infoOrdenesDireccion->telefono;
                $info->referencia = $infoOrdenesDireccion->referencia;
            }

            return ['success' => 1, 'ordenes' => $arrayOrdenes, 'hayordenes' => $conteo];
        }else{
            return ['success' => 2];
        }
    }



    public function iniciarEntregaOrden(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'idorden' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($info = Ordenes::where('id', $request->idorden)->first()){


            // REGLA: ORDENES NO CANCELADAS

            if($info->estado_cancelada == 1){
                $titulo = "No Disponible";
                $mensaje = "La orden fue cancelada";
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // REGLA: LA ORDEN NO HA SIDO PREPARADA POR EL RESTAURANTE

            if($info->estado_preparada == 0){
                $titulo = "No Disponible";
                $mensaje = "La orden no esta lista para entrega";
                return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // LA ORDEN PUEDE PROCEDER
            if($info->estado_camino == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $info->id)->update(['estado_camino' => 1,
                    'fecha_camino' => $fecha]);


                // NOTIFICACION AL CLIENTE
                $infoCliente = Clientes::where('id', $info->id_cliente)->first();

                if($infoCliente->token_fcm != null){

                    $tituloNoti = "Orden #" . $request->ordenid . " En Camino";
                    $mensajeNoti = "El Motorista se Dirige a su Dirección";


                }

                $titulo = "Iniciado";
                $mensaje = "Seguir el Proceso en Ordenes de Entrega";

                return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }else{

                // SIEMPRE DECIR QUE VA EN CAMINO
                return ['success' => 3];
            }
        }else{
            return ['success' => 99];
        }
    }



    public function listadoOrdenesEstoyEntregando(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoMotorista = MotoristasServicios::where('id', $request->id)->first()){

            $arrayOrdenes = DB::table('ordenes_motoristas AS om')
                ->join('ordenes AS o', 'om.id_ordenes', '=', 'o.id')
                ->where('estado_camino', 1)
                ->where('estado_entregada', 0)
                ->where('estado_cancelada', 0)
                ->where('om.id_motorista', $infoMotorista->id)
                ->get();

            $conteo = 0;

            // sumar mas envio
            foreach($arrayOrdenes as $info) {
                $conteo++;

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

                $estado = "Orden en Entrega";

                $info->estado = $estado;

                $info->haycupon = $haycupon;
                $info->cliente = $infoOrdenesDireccion->nombre;
                $info->direccion = $infoOrdenesDireccion->direccion;
                $info->telefono = $infoOrdenesDireccion->telefono;
                $info->referencia = $infoOrdenesDireccion->referencia;
            }

            return ['success' => 1, 'ordenes' => $arrayOrdenes, 'hayordenes' => $conteo];
        }else{
            return ['success' => 2];
        }
    }




    public function finalizarOrden(Request $request){


        $reglaDatos = array(
            'idorden' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoOrden = Ordenes::where('id', $request->idorden)->first()){


            if($infoOrden->estado_entregada == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $infoOrden->id)->update(['estado_entregada' => 1,
                    'fecha_entregada' => $fecha]);


                // NOTIFICACION AL CLIENTE
                $infoCliente = Clientes::where('id', $infoOrden->id_cliente)->first();

                if($infoCliente->token_fcm != null){

                    $tituloNoti = "Orden #" . $request->ordenid . " En Camino";
                    $mensajeNoti = "El Motorista se Dirige a su Dirección";


                }

                $titulo = "Orden Finalizada";
                $mensaje = "Gracias";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }else{


                $titulo = "Orden Finalizada";
                $mensaje = "Gracias";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

        }else{
            return ['success' => 2];
        }


    }


    public function listadoCompletadasHoyMotorista(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = MotoristasServicios::where('id', $request->id)->first()){

            $fecha = Carbon::now('America/El_Salvador');


            $arrayOrdenes = DB::table('ordenes AS o')
                ->join('ordenes_motoristas AS om', 'om.id_ordenes', '=', 'o.id')
                ->select('o.id', 'o.fecha_orden', 'om.id_motorista')
                ->where('om.id_motorista', $infoUsuario->id)
                ->where('o.estado_entregada', 1)
                ->where('o.estado_cancelada', 0)
                ->whereDate('o.fecha_orden', $fecha)
                ->get();

            $pilaIdOrdenes = array();
            foreach ($arrayOrdenes as $info){
                array_push($pilaIdOrdenes, $info->id);
            }


            $arrayOrdenes = Ordenes::whereIn('id', $pilaIdOrdenes)
                ->orderBy('id', 'DESC')
                ->get();

            $conteo = 0;

            foreach($arrayOrdenes as $info){
                $conteo++;

                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

                $info->fecha_preparada = date("h:i A d-m-Y", strtotime($info->fecha_orden));

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

            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 99];
        }

    }



    public function listadoCanceladasHoyMotorista(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = MotoristasServicios::where('id', $request->id)->first()){

            $fecha = Carbon::now('America/El_Salvador');


            $arrayOrdenes = DB::table('ordenes AS o')
                ->join('ordenes_motoristas AS om', 'om.id_ordenes', '=', 'o.id')
                ->select('o.id', 'o.fecha_orden', 'om.id_motorista')
                ->where('om.id_motorista', $infoUsuario->id)
                ->where('o.estado_cancelada', 1)
                ->whereDate('o.fecha_orden', $fecha)
                ->get();

            $pilaIdOrdenes = array();
            foreach ($arrayOrdenes as $info){
                array_push($pilaIdOrdenes, $info->id);
            }


            $arrayOrdenes = Ordenes::whereIn('id', $pilaIdOrdenes)
                ->orderBy('id', 'DESC')
                ->get();

            $conteo = 0;

            foreach($arrayOrdenes as $info){
                $conteo++;

                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

                $info->fecha_preparada = date("h:i A d-m-Y", strtotime($info->fecha_orden));

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

            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 99];
        }
    }




    public function historialOrdenesMotoristas(Request $request){

        $reglaDatos = array(
            'id' => 'required',
            'fecha1' => 'required',
            'fecha2' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoUsuario = MotoristasServicios::where('id', $request->id)->first()){

            $date1 = Carbon::parse($request->fecha1)->startOfDay();
            $date2 = Carbon::parse($request->fecha2)->endOfDay();

            // todas las ordenes por fecha

            $arrayOrdenes = DB::table('ordenes AS o')
                ->join('ordenes_motoristas AS om', 'om.id_ordenes', '=', 'o.id')
                ->select('o.id', 'o.fecha_orden', 'om.id_motorista')
                ->where('om.id_motorista', $infoUsuario->id)
                ->whereBetween('o.fecha_orden', array($date1, $date2))
                ->get();

            $pilaIdOrdenes = array();
            foreach ($arrayOrdenes as $info){
                array_push($pilaIdOrdenes, $info->id);
            }

            $arrayOrdenes = Ordenes::whereIn('id', $pilaIdOrdenes)
                ->orderBy('id', 'DESC')
                ->get();

            $conteo = 0;

            foreach($arrayOrdenes as $info){
                $conteo++;

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
                $info->referencia = $infoOrdenesDireccion->referencia;


                $estado = "Orden Pendiente";

                // POR PRIORIDAD

                if($info->estado_preparada == 1){
                    $estado = "Orden Lista para Entrega";
                }

                if($info->estado_camino == 1){
                    $estado = "Orden Entregandose";
                }

                if($info->estado_entregada == 1){
                    $estado = "Orden Entregada al Cliente";
                }

                if($info->estado_cancelada == 1){
                    $estado = "Orden Cancelada";
                }

                $info->estado = $estado;
            }


            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes, ];
        }else{
            return ['success' => 2];
        }

    }


    public function informacionNotificaciones(Request $request){

        $rules = array(
            'id' => 'required', // id motorista
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if($info = MotoristasServicios::where('id', $request->id)->first()){

            $mensaje = "Estado de Recibir Notifacaciones";

            return ['success' => 1, 'opcion' => $info->notificacion, 'mensaje' => $mensaje];
        }else{
            return ['success' => 2];
        }


    }

    public function editarNotificaciones(Request $request){

        $rules = array(
            'id' => 'required', // id motorista
            'disponible' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if(MotoristasServicios::where('id', $request->id)->first()){

            MotoristasServicios::where('id', $request->id)->update([
                'notificacion' => $request->disponible]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }


    }



}
