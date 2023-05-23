<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CarritoCallCenterExtra;
use App\Models\CarritoCallCenterTemporal;
use App\Models\Categorias;
use App\Models\DireccionesCallCenter;
use App\Models\Productos;
use App\Models\Servicios;
use App\Models\SubCategorias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallCenterController extends Controller
{


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
                ->select('p.id', 'sc.posicion', 'sc.nombre AS nombresubcate', 'p.descripcion', 'p.imagen', 'p.utiliza_imagen', 'p.precio', 'p.nombre')
                ->where('sc.id_categorias', $idPrimeraCategoria)
                ->where('sc.activo', 1)
                ->where('p.activo', 1)
                ->orderBy('sc.posicion', 'ASC')
                ->get();

            $arrayCarrito = CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->get();

            foreach ($arrayCarrito as $info){

                $infoProducto = Productos::where('id', $info->id_producto)->first();

                $info->nombre = $infoProducto->nombre;

                $multi = $infoProducto->precio * $info->cantidad;

                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');
                $info->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', ',');
            }



            return view('backend.admin.callcenter.menucontrol.vistamenucontrol', compact('arrayCategorias',
                'infoDireccion', 'nombreRestaurante', 'arrayProductos', 'idPrimeraCategoria', 'arrayCarrito'));
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

            $arrayCarrito = CarritoCallCenterExtra::where('id_carrito_call_tempo', $infoCarrito->id)->get();

            foreach ($arrayCarrito as $info) {

                $infoProducto = Productos::where('id', $info->id_producto)->first();

                $info->nombre = $infoProducto->nombre;

                $multi = $infoProducto->precio * $info->cantidad;

                $info->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');
                $info->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', ',');
            }


            return view('backend.admin.callcenter.generarorden.tablagenerarorden', compact('arrayCarrito'));

        }else{
            return "No se encontro Carrito de Compras. Recargar la Página";
        }
    }



}
