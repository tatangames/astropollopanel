<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CarritoCallCenterExtra;
use App\Models\CarritoCallCenterTemporal;
use App\Models\DireccionesCallCenter;
use App\Models\DireccionesRestaurante;
use App\Models\Servicios;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallCenterDireccionesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexListadoDirecciones(){

        $restaurantes = Servicios::orderBy('nombre')->get();

        return view('backend.admin.callcenter.direcciones.vistaeditardireccion', compact('restaurantes'));
    }



    public function tablaListadoDirecciones(){


        $listado = DireccionesCallCenter::orderBy('telefono')->get();

        foreach ($listado as $info){

            if($info->id_zonas != null){
                $infoZona = Zonas::where('id', $info->id_zonas)->first();
                $info->nombrezona = $infoZona->nombre;
            }else{
                $info->nombrezona = "";
            }

            $infoServicios = Servicios::where('id', $info->id_servicios)->first();

            $info->nombreservicio = $infoServicios->nombre;
        }


        return view('backend.admin.callcenter.direcciones.tablaeditardireccion', compact('listado'));
    }


    public function informacionDireccionCallCenter(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionesCallCenter::where('id', $request->id)->first()){

            // listado de zonas del servicio
            $arrayZona = ZonasServicio::where('id_servicios', $info->id_servicios)->get();

            foreach ($arrayZona as $data){
                $infoZona = Zonas::where('id', $data->id_zonas)->first();
                $data->nombrezona = $infoZona->nombre;
            }

            return ['success' => 1, 'info' => $info, 'zonas' => $arrayZona];
        }else{
            return ['success' => 2];
        }
    }


    public function cambiarRestauranteDireccion(Request $request){


        $regla = array(
            'id' => 'required',
            'idservicio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionesCallCenter::where('id', $request->id)->first()){

            DireccionesCallCenter::where('id', $info->id)->update([
                'id_servicios' => $request->idservicio,
                'id_zonas' => null,
            ]);


            // BORRAR CARRITO DE COMPRAS SI TUVIERA ESTE CLIENTE

            // porque cada admin de call center puede tener direcion registrada
            $arrayCarrito = CarritoCallCenterTemporal::where('id_direccion', $info->id)->get();

            foreach ($arrayCarrito as $data){

                // ELIMINA TODOS LOS PRODUCTOS PARA CADA CARRITO DE DIRECCIONES DEL MISMO
                // callcenter 1 puede tener este mismo usuario
                // callcenter 2 puede tener este mismo usuario en carrito
                CarritoCallCenterExtra::where('id_carrito_call_tempo', $data->id)->delete();
            }

            // BORRAR EL CARRITO
            CarritoCallCenterTemporal::where('id_direccion', $info->id)->delete();

            return ['success' => 1];

        }else{
            return ['success' => 2];
        }
    }



    public function editarDireccionCallCenter(Request $request){

        $regla = array(
            'id' => 'required',
            'idzona' => 'required',
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($infoDirec = DireccionesCallCenter::where('id', $request->id)->first()){

            DireccionesCallCenter::where('id', $infoDirec->id)->update([
                'id_zonas' => $request->idzona,
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'punto_referencia' => $request->referencia,
                'telefono' => $request->telefono,
            ]);

            // COMO NO CAMBIA DE RESTAURANTE NO ES NECESARIO BORRAR CARRITO SI LO TUVIERA
            // ASIGNADO CON ESA DIRECCION

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    //****************************************************************************************


    public function indexListadoDireccionesRestaurante(){

        $restaurantes = Servicios::orderBy('nombre')->get();

        return view('backend.admin.callcenter.direccionservicio.vistadireccionservicio', compact('restaurantes'));
    }



    public function tablaListadoDireccionesRestaurante(){


        $listado = DireccionesRestaurante::orderBy('direccion')->get();

        foreach ($listado as $info){

            $infoServicios = Servicios::where('id', $info->id_servicio)->first();
            $info->nombreservicio = $infoServicios->nombre;
        }

        return view('backend.admin.callcenter.direccionservicio.tabladireccionservicio', compact('listado'));

    }


    public function nuevaDireccionParaRestaurante(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'direccion' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        $dato = new DireccionesRestaurante();
        $dato->id_servicio = $request->idservicio;
        $dato->direccion = $request->direccion;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }



    public function informacionDireccionRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionesRestaurante::where('id', $request->id)->first()){

            // listado de restaurantes
            $arrayServicio = Servicios::orderBy('nombre')->get();

            return ['success' => 1, 'info' => $info, 'servicios' => $arrayServicio];
        }else{
            return ['success' => 2];
        }
    }



    public function editarDireccionRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
            'idservicio' => 'required',
            'direccion' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        if($info = DireccionesRestaurante::where('id', $request->id)->first()){

            DireccionesRestaurante::where('id', $info->id)->update([
                'id_servicio' => $request->idservicio,
                'direccion' => $request->direccion,
            ]);


            return ['success' => 1];

        }else{
            return ['success' => 99];
        }
    }



    public function borrarDireccionRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        if($info = DireccionesRestaurante::where('id', $request->id)->first()){

            DireccionesRestaurante::where('id', $info->id)->delete();

            return ['success' => 1];

        }else{
            // siempre regresar que fue borrada
            return ['success' => 1];
        }
    }




    //********************************

    public function indexListadoDireccionSinzona(){

        return view('backend.admin.callcenter.direccionsinzona.vistadireccionsinzona');
    }


    public function tablaListadoDireccionSinzona(Request $request){

        $listado = DireccionesCallCenter::where('id_zonas', '=', null)->get();

        foreach ($listado as $info){
            $infoServicio = Servicios::where('id', $info->id_servicios)->first();

            $info->restaurante = $infoServicio->nombre;
        }

        return view('backend.admin.callcenter.direccionsinzona.tabladireccionsinzona', compact('listado'));
    }



    public function infoDireccionSinZona(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionesCallCenter::where('id', $request->id)->first()){

            $lista = ZonasServicio::where('id_servicios', $info->id_servicios)->get();

            foreach ($lista as $data){
                $infoZona = Zonas::where('id', $data->id_zonas)->first();
                $data->nombrezona = $infoZona->nombre;
            }

            return ['success' => 1, 'info' => $info, 'zonas' => $lista];
        }else{
            return ['success' => 2];
        }
    }



    public function editarDireccionSinZona(Request $request){


        $regla = array(
            'id' => 'required',
            'idzona' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = DireccionesCallCenter::where('id', $request->id)->first()){

            DireccionesCallCenter::where('id', $info->id)->update([
                'id_zonas' => $request->idzona,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }


    }



}
