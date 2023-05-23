<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CallCenterCliente;
use App\Models\CarritoCallCenterExtra;
use App\Models\CarritoCallCenterTemporal;
use App\Models\Categorias;
use App\Models\DireccionesCallCenter;
use App\Models\HorarioServicio;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Productos;
use App\Models\Servicios;
use App\Models\SubCategorias;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallCenterController extends Controller
{



    public function __construct(){
        $this->middleware('auth');
    }


    public function indexGenerarOrden(){

        $restaurantes = Servicios::orderBy('nombre')->get();

        return view('backend.admin.callcenter.generarorden.vistagenerarorden', compact('restaurantes'));
    }


    // BUSCAR POR NUMERO PARA ENCONTRAR CLIENTE
    public function informacionClientePorNumero(Request $request){

        $rules = array(
            'numero' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        // PARA VER SI HAY Y MOSTRAR EL ARRAY DE DIRECCIONES
        if(DireccionesCallCenter::where('telefono', $request->numero)->first()){

            $arrayDirecciones = DireccionesCallCenter::where('telefono', $request->numero)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($arrayDirecciones as $info){
                $infoServicio = Servicios::where('id', $info->id_servicios)->first();
                $info->restaurante = $infoServicio->nombre;
            }

            return ['success' => 1, 'direcciones' => $arrayDirecciones];
        }else{
            // NUMERO SIN DIRECCIONES
            return ['success' => 2];
        }
    }



    public function nuevaDireccionCliente(Request $request){

        $regla = array(
            'servicio' => 'required',
            'nombre' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'referencia' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        DB::beginTransaction();

        try {

            $usuario = new DireccionesCallCenter();
            $usuario->id_servicios = $request->servicio;
            $usuario->nombre = $request->nombre;
            $usuario->direccion = $request->direccion;
            $usuario->punto_referencia = $request->referencia;
            $usuario->telefono = $request->telefono;
            $usuario->save();

            // BORRAR CARRITO TEMPORAL

            $idSession = Auth::id();

            if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){
                CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->delete();
                CarritoCallCenterTemporal::where('id_callcenter', $idSession)->delete();
            }

            // CREARLE UN CARRITO SIN PRODUCTOS

            $carrito = new CarritoCallCenterTemporal();
            $carrito->id_callcenter = $idSession;
            $carrito->id_direccion = $usuario->id;
            $carrito->save();

            DB::commit();

            return ['success' => 1];

        } catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function todoMenuRestauranteyCarrito(){

        $idSession = Auth::id();

        if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){

            $getValores = Carbon::now('America/El_Salvador');
            $hora = $getValores->format('H:i:s');


            // OBTENER DIRECCION PARA SACAR ID DE RESTAURANTE
            $infoDireccion = DireccionesCallCenter::where('id', $infoCarrito->id_direccion)->first();

            $infoServicios = Servicios::where('id', $infoDireccion->id_servicios)->first();
            $nombreRestaurante = $infoServicios->nombre;

            //*************

            $pilaIdCategorias = array();

            $arrayCategoriasHorario = Categorias::where('id_servicios', $infoDireccion->id_servicios)
                ->where('activo', 1)
                ->where('usa_horario', 1)
                ->where('hora_abre', '<=', $hora)
                ->where('hora_cierra', '>=', $hora)
                ->get();

            foreach ($arrayCategoriasHorario as $info){
                array_push($pilaIdCategorias, $info->id);
            }


            //**************

            $arrayCategoriasNoHorario = Categorias::where('id_servicios', $infoDireccion->id_servicios)
                ->where('activo', 1)
                ->get();

            foreach ($arrayCategoriasNoHorario as $info){
                array_push($pilaIdCategorias, $info->id);
            }


            $arrayCategorias = Categorias::whereIn('id', $pilaIdCategorias)
                ->orderBy('nombre')
                ->get();

            $idPrimeraCategoria = 0;
            foreach ($arrayCategorias as $dato){
                $idPrimeraCategoria = $dato->id;
                break;
            }

            $arrayProductos = DB::table('sub_categorias AS sc')
                ->join('productos AS p', 'p.id_subcategorias', '=', 'sc.id')
                ->select('p.id', 'sc.posicion', 'sc.nombre AS nombresubcate', 'p.activo', 'p.descripcion', 'p.imagen', 'p.utiliza_imagen', 'p.precio', 'p.nombre')
                ->where('sc.id_categorias', $idPrimeraCategoria)
                ->where('sc.activo', 1)
                ->where('p.activo', 1)
                ->orderBy('sc.posicion', 'ASC')
                ->get();

            $totalCarrito = 0;

            $arrayCarrito = CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->get();

            foreach ($arrayCarrito as $info){

                $infoProducto = Productos::where('id', $info->id_producto)->first();

                $info->nombre = $infoProducto->nombre;

                $multi = $infoProducto->precio * $info->cantidad;

                $totalCarrito = $totalCarrito + $multi;

                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');
                $info->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', ',');

                $estadoProductoActivo = 1;

                // PRODUCTO NO ACTIVO
                if($infoProducto->activo == 0){
                    $estadoProductoActivo = 0;
                }

                // CONOCER SI LA SUB CATEGORIA ESTA ACTIVA
                $infoSubCate = SubCategorias::where('id', $infoProducto->id_subcategorias)->first();

                if($infoSubCate->activo == 0){

                    $estadoProductoActivo = 0;
                }

                // CONOCER SI LA CATEGORIA DEL PRODUCTO ESTA ACTIVA
                $infoCategoria = Categorias::where('id', $infoSubCate->id_categorias)->first();

                if($infoCategoria->activo == 0){

                    $estadoProductoActivo = 0;
                }


                if($infoCategoria->usa_horario == 1){
                    // CONOCER SI LA CATEGORIA TIENE HORARIO Y VER SI ESTA DISPONIBLE
                    $horaCategoria = Categorias::where('id', $infoSubCate->id_categorias)
                        ->where('activo', 1)
                        ->where('usa_horario', 1)
                        ->where('hora_abre', '<=', $hora)
                        ->where('hora_cierra', '>=', $hora)
                        ->get();

                    if(count($horaCategoria) >= 1){
                        // ABIERTO
                    }else{

                        $estadoProductoActivo = 0;
                    }
                }

                $info->estadoProductoActivo = $estadoProductoActivo;
            }

            $totalCarrito = '$' . number_format((float)$totalCarrito, 2, '.', ',');


            //*************************************************************************

            $estadoRestaurante = 1; // abierto
            $mensajeRestaurante = "Abierto";

            // VERIFICAR ESTADOS DE ABRE / CIERRE RESTAURANTE

            $numSemana = [
                    0 => 1, // domingo
                    1 => 2, // lunes
                    2 => 3, // martes
                    3 => 4, // miercoles
                    4 => 5, // jueves
                    5 => 6, // viernes
                    6 => 7, // sabado
                ];

                $getDiaHora = $getValores->dayOfWeek;
                $diaSemana = $numSemana[$getDiaHora];

                // REGLA: VALIDAR HORARIO DEL RESTAURANTE
                $horario = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.id_servicios')
                    ->where('h.id_servicios', $infoServicios->id)
                    ->where('h.dia', $diaSemana)
                    ->where('h.hora1', '<=', $hora)
                    ->where('h.hora2', '>=', $hora)
                    ->get();

                if(count($horario) >= 1){

                }else{
                    // cerrado

                    $estadoRestaurante = 0;
                    $mensajeRestaurante = "Horario de Entrega Cerrado";
                }

                // REGLA: VALIDAR DIA CERRADO DEL RESTAURANTE

                $cerradoHoy = HorarioServicio::where('id_servicios', $infoServicios->id)
                    ->where('dia', $diaSemana)
                    ->first();

                if($cerradoHoy->cerrado == 1){
                    $estadoRestaurante = 0;
                    $mensajeRestaurante = "Hoy esta Cerrado";
                }



                // SI ESTA DIRECCION YA TIENE ASIGNADA UNA ZONA SE DEBE VERIFICAR

            $textoMinimoCompra = "$0.00";

            if($infoDireccion->id_zonas != null){

                $infoZona = Zonas::where('id', $infoDireccion->id_zonas)->first();
                $textoMinimoCompra = '$' . number_format((float)$infoZona->minimo, 2, '.', ',');


                // REGLA: EL RESTAURANTE TIENE CERRADO ESTA ZONA
                if($infoZona->saturacion == 1){
                    $estadoRestaurante = 0;
                    $mensajeRestaurante = "La zona de entrega esta cerrada por el momento";
                }

                $horaZona = Zonas::where('id', $infoZona->id)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();

                if(count($horaZona) >= 1){
                    // ABIERTO
                }else{
                    $estadoRestaurante = 0;
                    $mensajeRestaurante = "El Horario a domicilio para  su Dirección esta cerrado";
                }
            }


            return view('backend.admin.callcenter.menucontrol.vistamenucontrol', compact('arrayCategorias',
                'infoDireccion', 'nombreRestaurante', 'arrayProductos', 'idPrimeraCategoria', 'arrayCarrito',
            'totalCarrito', 'estadoRestaurante', 'mensajeRestaurante', 'textoMinimoCompra'));
        }
        else{
            return view('backend.admin.callcenter.menucontrol.vistanohaycarrito');
        }
    }



    public function seleccionarDireccionCliente(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        DB::beginTransaction();

        try {

            // BORRAR CARRITO TEMPORAL SI EXISTIA

            $idSession = Auth::id();

            if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){
                CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->delete();
                CarritoCallCenterTemporal::where('id_callcenter', $idSession)->delete();
            }

            // CREARLE UN CARRITO SIN PRODUCTOS

            $carrito = new CarritoCallCenterTemporal();
            $carrito->id_callcenter = $idSession;
            $carrito->id_direccion = $request->id;
            $carrito->save();

            DB::commit();

            return ['success' => 1];

        } catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }

    }


    public function listadoProductosPorCategoria($idcate){

        $arraySubCategorias = SubCategorias::where('id_categorias', $idcate)->get();

        $pilaIdSubCate = array();

        foreach ($arraySubCategorias as $info){
            array_push($pilaIdSubCate, $info->id);
        }

        $arrayProductos = Productos::where('id_subcategorias', $pilaIdSubCate)->get();

        foreach ($arrayProductos as $info){
            $info->precio = '$' . number_format((float)$info->precio, 2, '.', ',');
        }

        return view('backend.admin.callcenter.menucontrol.vistasolotablaproductos', compact('arrayProductos'));
    }


    public function informacionProducto(Request $request){

        $regla = array(
            'idproducto' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Productos::where('id', $request->idproducto)->first()){

            return ['success' => 1, 'producto' => $info];
        }else{
            return ['success' => 2];
        }
    }



    public function guardarProductoEnCarrito(Request $request){

        $regla = array(
            'idproducto' => 'required',
            'cantidad' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        $idSession = Auth::id();

        // SI TENGO CARRITO
        if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){

            $registro = new CarritoCallCenterExtra();
            $registro->id_carrito_call_tempo = $infoCarrito->id;
            $registro->id_producto = $request->idproducto;
            $registro->nota_producto = $request->nota;
            $registro->cantidad = $request->cantidad;
            $registro->save();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    function borrarFilaProducto(Request $request){

        $regla = array(
            'idfila' => 'required', // id fila
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = CarritoCallCenterExtra::where('id', $request->idfila)->first()){
            CarritoCallCenterExtra::where('id', $info->id)->delete();
        }

        return ['success' => 1];
    }




    public function recargarTablaCarrito(){

        $idSession = Auth::id();

        if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()) {

            $totalCarrito = 0;

            $arrayCarrito = CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->get();

            $getValores = Carbon::now('America/El_Salvador');
            $hora = $getValores->format('H:i:s');

            foreach ($arrayCarrito as $info) {

                $infoProducto = Productos::where('id', $info->id_producto)->first();

                $info->nombre = $infoProducto->nombre;

                $multi = $infoProducto->precio * $info->cantidad;

                $totalCarrito = $totalCarrito + $multi;

                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');
                $info->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', ',');


                $estadoProductoActivo = 1;

                // PRODUCTO NO ACTIVO
                if($infoProducto->activo == 0){
                    $estadoProductoActivo = 0;
                }

                // CONOCER SI LA SUB CATEGORIA ESTA ACTIVA
                $infoSubCate = SubCategorias::where('id', $infoProducto->id_subcategorias)->first();

                if($infoSubCate->activo == 0){

                    $estadoProductoActivo = 0;
                }

                // CONOCER SI LA CATEGORIA DEL PRODUCTO ESTA ACTIVA
                $infoCategoria = Categorias::where('id', $infoSubCate->id_categorias)->first();

                if($infoCategoria->activo == 0){

                    $estadoProductoActivo = 0;
                }


                if($infoCategoria->usa_horario == 1){
                    // CONOCER SI LA CATEGORIA TIENE HORARIO Y VER SI ESTA DISPONIBLE
                    $horaCategoria = Categorias::where('id', $infoSubCate->id_categorias)
                        ->where('activo', 1)
                        ->where('usa_horario', 1)
                        ->where('hora_abre', '<=', $hora)
                        ->where('hora_cierra', '>=', $hora)
                        ->get();

                    if(count($horaCategoria) >= 1){
                        // ABIERTO
                    }else{
                        $estadoProductoActivo = 0;
                    }
                }

                $info->estadoProductoActivo = $estadoProductoActivo;
            }

            $totalCarrito = '$' . number_format((float)$totalCarrito, 2, '.', ',');

            return view('backend.admin.callcenter.generarorden.tablagenerarorden', compact('arrayCarrito', 'totalCarrito'));

        }else{
            return "No se encontro Carrito de Compras. Recargar la Página";
        }
    }




    public function borrarYDeseleccionarTodo(){

        $idSession = Auth::id();

        if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){
            CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->delete();
            CarritoCallCenterTemporal::where('id_callcenter', $idSession)->delete();
        }

        return ['success' => 1];
    }



    public function informacionProductoFilaCarrito(Request $request){

        $regla = array(
            'idfila' => 'required', // id fila
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = CarritoCallCenterExtra::where('id', $request->idfila)->first()){

            $infoProducto = Productos::where('id', $info->id_producto)->first();

            $multi = $infoProducto->precio * $info->cantidad;

            $multiplicado = '$' . number_format((float)$multi, 2, '.', ',');

            return ['success' => 1, 'info' => $info,
                'producto' => $infoProducto,
                'multiplicado' => $multiplicado];
        }else{
            return ['success' => 2];
        }
    }



    public function actualizarFilaCarritoCompras(Request $request){

        $regla = array(
            'idfila' => 'required',
            'cantidad' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($infoCarrito = CarritoCallCenterExtra::where('id', $request->idfila)->first()){

            CarritoCallCenterExtra::where('id', $infoCarrito->id)->update([
                'nota_producto' => $request->nota,
                'cantidad' => $request->cantidad,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    public function enviarOrdenFinal(Request $request){

        $idSession = Auth::id();



            DB::beginTransaction();

            try {



                if($infoCarrito = CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()) {

                    $conteo = CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->count();

                    if ($conteo <= 0) {
                        $mensaje = "Carrito de compras esta vacío";
                        return ['success' => 1, 'mensaje' => $mensaje];
                    }

                    $infoDireccion = DireccionesCallCenter::where('id', $infoCarrito->id_direccion)->first();

                    $infoServicio = Servicios::where('id', $infoDireccion->id_servicios)->first();

                    $numSemana = [
                        0 => 1, // domingo
                        1 => 2, // lunes
                        2 => 3, // martes
                        3 => 4, // miercoles
                        4 => 5, // jueves
                        5 => 6, // viernes
                        6 => 7, // sabado
                    ];

                    $getValores = Carbon::now('America/El_Salvador');
                    $getDiaHora = $getValores->dayOfWeek;
                    $diaSemana = $numSemana[$getDiaHora];
                    $hora = $getValores->format('H:i:s');


                    // VERIFICAR ZONA SI TIENE YA ASIGNADA
                    if ($infoDireccion->id_zonas != null) {

                        $infoZona = Zonas::where('id', $infoDireccion->id_zonas)->first();

                        // REGLA: EL RESTAURANTE TIENE CERRADO ESTA ZONA
                        if ($infoZona->saturacion == 1) {
                            $mensaje = "La zona asignada a esta direccion esta cerrada por el momento. No hay entregas a domicilio";
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }

                        // REGLA: VERIFICAR HORARIO DE LA ZONA
                        $horaZona = Zonas::where('id', $infoZona->id)
                            ->where('hora_abierto_delivery', '<=', $hora)
                            ->where('hora_cerrado_delivery', '>=', $hora)
                            ->get();

                        if (count($horaZona) >= 1) {
                            // ABIERTO
                        } else {

                            $abre = date("h:i A", strtotime($infoZona->hora_abierto_delivery));
                            $cierra = date("h:i A", strtotime($infoZona->hora_cerrado_delivery));

                            $mensaje = "El Horario de zona para su Dirección esta cerrado. No hay entregas a domicilio. Horario es: " . $abre . " / " . $cierra;
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }
                    }


                    // REGLA DE PRODUCTOS DISPONIBLE
                    // CATEGORIAS ACTIVAS (CON HORARIO Y SIN HORARAIO)
                    // SUB CATEGORIAS ACTIVAS
                    // PRODUCTO ACTIVO


                    $producto = DB::table('productos AS p')
                        ->join('carrito_callcenter_extra AS c', 'c.id_producto', '=', 'p.id')
                        ->select('p.id AS productoID', 'p.nombre', 'c.cantidad',
                            'p.imagen', 'p.precio', 'p.activo', 'c.nota_producto', 'c.id AS carritoid', 'p.utiliza_imagen', 'p.id_subcategorias')
                        ->where('c.id_carrito_call_tempo', $infoCarrito->id)
                        ->get();


                    $totalCarrito = 0;

                    // verificar cada producto
                    foreach ($producto as $pro) {

                        // PRODUCTO NO ACTIVO
                        if ($pro->activo == 0) {

                            $mensaje = "El Producto: " . $pro->nombre . " No esta disponible";
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }

                        // CONOCER SI LA SUB CATEGORIA ESTA ACTIVA
                        $infoSubCate = SubCategorias::where('id', $pro->id_subcategorias)->first();

                        if ($infoSubCate->activo == 0) {
                            $mensaje = "El Producto: " . $pro->nombre . " La sub categoría esta desactivada";
                            return ['success' => 1, 'mensaje' => $mensaje];

                        }

                        // CONOCER SI LA CATEGORIA DEL PRODUCTO ESTA ACTIVA
                        $infoCategoria = Categorias::where('id', $infoSubCate->id_categorias)->first();

                        if ($infoCategoria->activo == 0) {

                            $mensaje = "El Producto: " . $pro->nombre . " La categoría esta desactivada";
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }


                        if ($infoCategoria->usa_horario == 1) {
                            // CONOCER SI LA CATEGORIA TIENE HORARIO Y VER SI ESTA DISPONIBLE
                            $horaCategoria = Categorias::where('id', $infoSubCate->id_categorias)
                                ->where('activo', 1)
                                ->where('usa_horario', 1)
                                ->where('hora_abre', '<=', $hora)
                                ->where('hora_cierra', '>=', $hora)
                                ->get();

                            if (count($horaCategoria) >= 1) {
                                // ABIERTO
                            } else {

                                $abre = date("h:i A", strtotime($infoCategoria->hora_abre));
                                $cierra = date("h:i A", strtotime($infoCategoria->hora_cierra));

                                $mensaje = "El Producto: " . $pro->nombre . " El Horario de categoría esta terminada. Horario es: " . $abre . " / " . $cierra;
                                return ['success' => 1, 'mensaje' => $mensaje];
                            }
                        }


                        $multi = $pro->precio * $pro->cantidad;
                        $totalCarrito = $totalCarrito + $multi;
                    }


                    // REGLA: VALIDAR HORARIO DEL RESTAURANTE


                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.id_servicios')
                        ->where('h.id_servicios', $infoServicio->id)
                        ->where('h.dia', $diaSemana)
                        ->where('h.hora1', '<=', $hora)
                        ->where('h.hora2', '>=', $hora)
                        ->get();

                    if (count($horario) >= 1) {

                    } else {
                        // cerrado

                        $titulo = "Nota";
                        $mensaje = "El Restaurante esta cerrado";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                    }


                    // REGLA: VALIDAD DIA CERRADO DEL RESTAURANTE

                    $cerradoHoy = HorarioServicio::where('id_servicios', $infoServicio->id)
                        ->where('dia', $diaSemana)
                        ->first();


                    if ($cerradoHoy->cerrado == 1) {
                        $mensaje = "El Restaurante esta cerrado este Día";
                        return ['success' => 1, 'mensaje' => $mensaje];
                    }

                    $tengoIdZona = null;


                    // REGLA: MINIMO DE COMPRA A LA ZONA
                    if ($infoDireccion->id_zonas != null) {

                        $infoZonaD = Zonas::where('id', $infoDireccion->id_zonas)->first();

                        $tengoIdZona = $infoZonaD->id;

                        if($totalCarrito < $infoZonaD->minimo){

                            $minimo = '$' . number_format((float)$infoZonaD->minimo, 2, '.', ',');

                            $mensaje = "El mínimo de compra para su dirección actual es . " . $minimo;
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }
                    }



                    // VALIDACION COMPLETA
                    $idSession = Auth::id();

                    $infoMicliente = CallCenterCliente::where('id_administrador', $idSession)->first();

                    // GUARDAR LA ORDEN

                    $fechaHoy = Carbon::now('America/El_Salvador');

                    $orden = new Ordenes();
                    $orden->id_cliente = $infoMicliente->id_cliente;
                    $orden->id_servicio = $infoServicio->id;
                    $orden->id_zona = $tengoIdZona;
                    $orden->nota_orden = $request->notaorden;
                    $orden->total_orden = $totalCarrito;
                    $orden->fecha_orden = $fechaHoy;
                    $orden->fecha_estimada = null;
                    $orden->estado_iniciada = 0;
                    $orden->fecha_iniciada = null;
                    $orden->estado_preparada = 0;
                    $orden->fecha_preparada = null;
                    $orden->estado_camino = 0;
                    $orden->fecha_camino = null;
                    $orden->estado_entregada = 0;
                    $orden->fecha_entregada = null;
                    $orden->nota_cancelada = null;
                    $orden->id_cupones = null;
                    $orden->id_cupones_copia = null;
                    $orden->total_cupon = null;
                    $orden->mensaje_cupon = null;
                    $orden->visible = 1;
                    $orden->cancelado_por = 0;

                    $orden->save();


                    // GUARDAR LA DIRECCION DEL CLIENTE

                    $dirCliente = new OrdenesDirecciones();
                    $dirCliente->id_ordenes = $orden->id;

                    $dirCliente->nombre = $infoDireccion->nombre;
                    $dirCliente->direccion = $infoDireccion->direccion;
                    $dirCliente->telefono = $infoDireccion->telefono;
                    $dirCliente->referencia = $infoDireccion->punto_referencia;
                    $dirCliente->latitud = null;
                    $dirCliente->longitud = null;
                    $dirCliente->latitudreal = null;
                    $dirCliente->longitudreal = null;
                    $dirCliente->appversion = "Generada de Call Center";

                    $dirCliente->save();



                    // guadar todos los productos de esa orden
                    foreach($producto as $p){

                        $data = array('id_ordenes' => $orden->id,
                            'id_producto' => $p->productoID,
                            'cantidad' => $p->cantidad,
                            'nota' => $p->nota_producto,
                            'precio' => $p->precio);
                        OrdenesDescripcion::insert($data);
                    }

                    // LA NOTIFICACION SE ENVIARA DESPUES DE CONFIRMAR ENTREGA








                    // BORRAR CARRITO DE COMPRAS
                    CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->delete();
                    CarritoCallCenterTemporal::where('id_callcenter', $idSession)->delete();

                     DB::commit();
                $mensaje = "Orden enviada";
                return ['success' => 2, 'mensaje' => $mensaje];

            }else{
                $mensaje = "No se encontro carrito de compras";
                return ['success' => 1, 'mensaje' => $mensaje];
            }



            } catch (\Throwable $e) {
                Log::info('eer ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
    }



}
