<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CarritoCallCenterExtra;
use App\Models\CarritoCallCenterTemporal;
use App\Models\Categorias;
use App\Models\DireccionesCallCenter;
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

        // para ocultar o mostar los contenedores, se debe verificar si tenemos direccion asignada,
        // que es lo mismo a tener carrito de compras

        $idSession = Auth::id();
        $tengocarrito = 0;
        if(CarritoCallCenterTemporal::where('id_callcenter', $idSession)->first()){
            $tengocarrito = 1;
        }

        return view('backend.admin.callcenter.generarorden.vistagenerarorden', compact('restaurantes',
        'tengocarrito'));
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

            return view('backend.admin.callcenter.menucontrol.vistamenucontrol', compact('arrayCategorias',
                'infoDireccion', 'nombreRestaurante', 'arrayProductos'));
        }
        else{
            return view('backend.admin.callcenter.menucontrol.vistanohaycarrito');
        }
    }





}
