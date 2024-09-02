<?php

namespace App\Http\Controllers\Api\Ordenes;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarNotificacion;
use App\Jobs\EnviarNotificacionRestaurante;
use App\Models\Categorias;
use App\Models\Clientes;
use App\Models\Cupones;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\OrdenesMotoristas;
use App\Models\OrdenesPremio;
use App\Models\Productos;
use App\Models\SubCategorias;
use App\Models\UsuariosServicios;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use OneSignal;

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


            if($request->idfirebase != null){
                UsuariosServicios::where('id', $infoUsuario->id)->update(['token_fcm' => $request->idfirebase]);
            }


            // actualizar ccada vez que entran
            $fecha = Carbon::now('America/El_Salvador');

            UsuariosServicios::where('id', $infoUsuario->id)->update([
                'fecha_entroapp' => $fecha]);


            $arrayOrdenes = Ordenes::where('estado_iniciada', 0)
                ->where('estado_cancelada', 0)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'ASC')
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


                $haypremio = 0;
                $textopremio = "";

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $info->id)->first()){
                    // si se canjeo premio

                    $haypremio = 1;
                    $textopremio = $infoOrdenPremio->nombre;
                }

                $info->haypremio = $haypremio;
                $info->textopremio = $textopremio;
            }

            return ['success' => 2, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
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


            return ['success' => 1, 'productos' => $lista];
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
                if($infoZona = Zonas::where('id', $infoOrden->id_zona)->first()){

                    $fechaInicioPreparar = Carbon::parse($fecha);
                    $horaEstimada = $fechaInicioPreparar->addMinute($infoZona->tiempo_extra);
                }else{

                    $horaEstimada = Carbon::now('America/El_Salvador');
                }

                //****************
                $fechaHoy = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $infoOrden->id)
                    ->update(['estado_iniciada' => 1,
                              'fecha_iniciada' => $fechaHoy,
                              'fecha_estimada' => $horaEstimada]);



                // NOTIFICACION ONE SIGNAL A CLIENTE

                $infoCliente = Clientes::where('id', $infoOrden->id_cliente)->first();

                if($infoCliente->token_fcm != null){

                    $tituloNoti = "Orden #" . $infoOrden->id . " Aceptada";
                    $mensajeNoti = "Su orden inicia su Preparación";



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


                    $AppId = config('googleapi.IdApp_Motorista');

                    $AppGrupoNotiPasivo = config('googleapi.IdGrupoPasivoMotorista');


                    $contents = array(
                        "en" => $mensajeNoti
                    );

                    $params = array(
                        'app_id' => $AppId,
                        'contents' => $contents,
                        'android_channel_id' => $AppGrupoNotiPasivo,
                        'include_player_ids' => is_array($pilaMotoristas) ? $pilaMotoristas : array($pilaMotoristas)
                    );

                    $params['headings'] = array(
                        "en" => $tituloNoti
                    );

                    OneSignal::sendNotificationCustom($params);
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



                // SE DEVULVEN LOS PUNTOS DEL PREMIO SI CANCELO EL CLIENTE

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $infoOrden->id)->first()){

                    $infoCliente = Clientes::where('id', $infoOrdenPremio->id_cliente)->first();

                    // SUMAR LOS PUNTOS AL CLUENTE
                    $suma = $infoCliente->puntos + $infoOrdenPremio->puntos;

                    Clientes::where('id', $infoCliente->id)->update([
                        'puntos' => $suma]);


                }




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

                        $tituloNoti = "Orden #" . $infoOrden->id . " Cancelada";
                        $mensajeNoti = "Fue cancelada por Restaurante";

                        // ENVIAR NOTIFICACION


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

                    DB::commit();

                    // Orden cancelada por restaurante

                    $tituloFinal = "Nota";
                    $mensajeFinal = "Orden cancelada correctamente";

                    return ['success' => 1, 'titulo' => $tituloFinal, 'mensaje' => $mensajeFinal];


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



                $haypremio = 0;
                $textopremio = "";

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $info->id)->first()){
                    // si se canjeo premio

                    $haypremio = 1;
                    $textopremio = $infoOrdenPremio->nombre;
                }

                $info->haypremio = $haypremio;
                $info->textopremio = $textopremio;
            }

            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 3];
        }
    }



    public function finalizarOrdenPorRestaurante(Request $request){

        $reglaDatos = array(
            'idorden' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoOrden = Ordenes::where('id', $request->idorden)->first()){

            // ORDEN ESTA CANCELADA
            if($infoOrden->estado_cancelada == 1){
                // solo decir que esta cancelada y no se mostrara ya
                $titulo = "Orden Cancelada";
                $mensaje = "Fue cancelada";
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // FINALIZAR LA ORDEN
            if($infoOrden->estado_preparada == 0){

                $fecha = Carbon::now('America/El_Salvador');


                Ordenes::where('id', $infoOrden->id)
                    ->update(['estado_preparada' => 1,
                        'fecha_preparada' => $fecha]);


                // NOTIFICACION ONE SIGNAL  A MOTORISTA QUE TIENE LA ORDEN SELECCIONADA

                if($infoOrdenMoto = OrdenesMotoristas::where('id_ordenes', $infoOrden->id)->first()){

                    // un motorista ya tiene la orden seleccionada
                    $infoMotorista = MotoristasServicios::where('id', $infoOrdenMoto->id_motorista)->first();

                    if($infoMotorista->token_fcm != null){

                        $tituloNoti = "Orden #" . $infoOrden->id;
                        $mensajeNoti = "Esta lista para Entrega";

                        // ENVIAR NOTIFICACIONES A MOTORISTA


                        $AppId = config('googleapi.IdApp_Motorista');

                        $AppGrupoNotiAlarma = config('googleapi.IdGrupoAlarmaMotorista');

                        $tokenMotorista = $infoMotorista->token_fcm;


                        $contents = array(
                            "en" => $mensajeNoti
                        );

                        $params = array(
                            'app_id' => $AppId,
                            'contents' => $contents,
                            'android_channel_id' => $AppGrupoNotiAlarma,
                            'include_player_ids' => is_array($tokenMotorista) ? $tokenMotorista : array($tokenMotorista)
                        );

                        $params['headings'] = array(
                            "en" => $tituloNoti
                        );

                        OneSignal::sendNotificationCustom($params);
                    }

                }else{

                    // NO HAY MOTORISTAS ASI QUE ENVIAR NOTIFICACION (POR RESTAURANTE)

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
                        $tituloNoti = "Una Orden Sin Motorista";
                        $mensajeNoti = "Por Favor Verificar";
                        // ENVIAR NOTIFICACIONES A TODOS LOS MOTORISTAS DEL RESTAURANTE


                        $AppId = config('googleapi.IdApp_Motorista');

                        $AppGrupoNotiAlarma = config('googleapi.IdGrupoAlarmaMotorista');


                        $contents = array(
                            "en" => $mensajeNoti
                        );

                        $params = array(
                            'app_id' => $AppId,
                            'contents' => $contents,
                            'android_channel_id' => $AppGrupoNotiAlarma,
                            'include_player_ids' => is_array($pilaMotoristas) ? $pilaMotoristas : array($pilaMotoristas)
                        );

                        $params['headings'] = array(
                            "en" => $tituloNoti
                        );

                        OneSignal::sendNotificationCustom($params);
                    }
                }


                $titulo = "Nota";
                $mensaje = "Orden Finalizada";

                // orden iniciada
                return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            $titulo = "Nota";
            $mensaje = "Orden Finalizada";

            // orden finalizada por defecto
            return ['success'=> 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }else{
            return ['success'=> 3];
        }
    }




    public function completadasHoyOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = UsuariosServicios::where('id', $request->id)->first()){

            $fecha = Carbon::now('America/El_Salvador');

            $arrayOrdenes = Ordenes::where('estado_preparada', 1)
                ->where('estado_cancelada', 0)
                ->whereDate('fecha_preparada', $fecha)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'ASC')
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
                $info->referencia = $infoOrdenesDireccion->referencia;


                $haypremio = 0;
                $textopremio = "";

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $info->id)->first()){
                    // si se canjeo premio

                    $haypremio = 1;
                    $textopremio = $infoOrdenPremio->nombre;
                }

                $info->haypremio = $haypremio;
                $info->textopremio = $textopremio;
            }

            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 99];
        }
    }


    public function canceladasHoyOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = UsuariosServicios::where('id', $request->id)->first()){

            $fecha = Carbon::now('America/El_Salvador');


            $arrayOrdenes = Ordenes::where('estado_cancelada', 1)
                ->whereDate('fecha_cancelada', $fecha)
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'ASC')
                ->get();

           $conteo = 0;

            foreach($arrayOrdenes as $info){
                $conteo++;

                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();

                $info->fecha_orden = date("h:i A d-m-Y", strtotime($info->fecha_orden));

                $info->fecha_cancelada = date("h:i A d-m-Y", strtotime($info->fecha_cancelada));

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


                $haypremio = 0;
                $textopremio = "";

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $info->id)->first()){
                    // si se canjeo premio

                    $haypremio = 1;
                    $textopremio = $infoOrdenPremio->nombre;
                }

                $info->haypremio = $haypremio;
                $info->textopremio = $textopremio;
            }

            return ['success' => 1, 'hayordenes' => $conteo, 'ordenes' => $arrayOrdenes];
        }else{
            return ['success' => 99];
        }
    }

    public function listadoDeCategorias(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoUsuario = UsuariosServicios::where('id', $request->id)->first()){

           $arrayCategorias = Categorias::where('id_servicios', $infoUsuario->id_servicios)
               ->orderBy('posicion', 'ASC')
               ->get();

            foreach($arrayCategorias as $info){

                $horaAbre = date("h:i A", strtotime($info->hora_abre));
                $horaCierra = date("h:i A", strtotime($info->hora_cierra));

                $info->horario = "Horario: " . $horaAbre . " / " . $horaCierra;

                if($info->activo == 1){
                    $info->estado = "Activo";
                }else{
                    $info->estado = "Desactivado";
                }


            }

            return ['success' => 1, 'categorias' => $arrayCategorias];
        }else{
            return ['success' => 99];
        }
    }


    public function actualizarEstadoCategoria(Request $request){

        $rules = array(
            'idcategoria' => 'required',
            'checkvalor' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoCategoria = Categorias::where('id', $request->idcategoria)->first()){

            Categorias::where('id', $infoCategoria->id)
                ->update(['activo' => $request->checkvalor]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }



    public function listadoDeProductosPorCategoria(Request $request){


        $reglaDatos = array(
            'idcategoria' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }


        if($infoCategoria = Categorias::where('id', $request->idcategoria)->first()){

            // listado de sub categorias

            $arraySubCategorias = SubCategorias::where('id_categorias', $infoCategoria->id)->get();

            $pilaIdSubCategorias = array();

            foreach ($arraySubCategorias as $info){
                array_push($pilaIdSubCategorias, $info->id);
            }

            $arrayProductos = Productos::whereIn('id_subcategorias', $pilaIdSubCategorias)
                ->orderBy('posicion', 'ASC')
                ->get();


            foreach ($arrayProductos as $info){

                if($info->activo == 1){
                    $info->estado = "Activado";
                }else{
                    $info->estado = "Desactivado";
                }
            }

            return ['success' => 1, 'productos' => $arrayProductos,];
        }
        else{

            return ['success' => 2];
        }
    }


    public function actualizarEstadoProducto(Request $request){


        $rules = array(
            'idproducto' => 'required',
            'checkvalor' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($infoProducto = Productos::where('id', $request->idproducto)->first()){

            Productos::where('id', $infoProducto->id)
                ->update(['activo' => $request->checkvalor]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }

    }



    public function historialOrdenesRestaurantes(Request $request)
    {

        $reglaDatos = array(
            'id' => 'required',
            'fecha1' => 'required',
            'fecha2' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if ($infoUsuario = UsuariosServicios::where('id', $request->id)->first()) {

            $date1 = Carbon::parse($request->fecha1)->startOfDay();
            $date2 = Carbon::parse($request->fecha2)->endOfDay();

            // todas las ordenes por fecha
            $arrayOrdenes = Ordenes::whereBetween('fecha_orden', array($date1, $date2))
                ->where('id_servicio', $infoUsuario->id_servicios)
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($arrayOrdenes as $info) {

                $info->fecha_orden = date("d-m-Y h:i A", strtotime($info->fecha_orden));

                $estado = "Orden Pendiente";

                // POR PRIORIDAD

                if ($info->estado_iniciada == 1) {
                    $estado = "Orden Preparandose";
                }

                if ($info->estado_preparada == 1) {
                    $estado = "Orden Finalizada";
                }

                if ($info->estado_entrega == 1) {
                    $estado = "Orden Entregada al Cliente";
                }

                if ($info->estado_cancelada == 1) {
                    $estado = "Orden Cancelada";
                }

                $info->estado = $estado;


                $infoOrdenesDireccion = OrdenesDirecciones::where('id_ordenes', $info->id)->first();


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




                $haypremio = 0;
                $textopremio = "";

                if($infoOrdenPremio = OrdenesPremio::where('id_ordenes', $info->id)->first()){
                    // si se canjeo premio

                    $haypremio = 1;
                    $textopremio = $infoOrdenPremio->nombre;
                }

                $info->haypremio = $haypremio;
                $info->textopremio = $textopremio;
            }


            return ['success' => 1, 'ordenes' => $arrayOrdenes];
        } else {
            return ['success' => 2];
        }
    }


    public function enviarNotificacionPropia(Request $request){

        $rules = array(
            'id' => 'required'
        );

        // tokenid

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}


        if($infoAfiliado = UsuariosServicios::where('id', $request->id)->first()){

            UsuariosServicios::where('id', $request->id)
                ->update(['token_fcm' => $request->tokenid]);

                if($infoAfiliado->token_fcm != null){

                    // ENVIAR NOTIFICACION

                    $mensaje = "Modo Prueba";
                    $titulo = "Se recibe notificaciones";

                    dispatch(new EnviarNotificacionRestaurante($infoAfiliado->token_fcm, $mensaje, $titulo));

                    return ['success' => 1];
                }
                else{
                    return ['success' => 2];
                }


        }
        else{
            return ['success' => 2];
        }


    }




}
