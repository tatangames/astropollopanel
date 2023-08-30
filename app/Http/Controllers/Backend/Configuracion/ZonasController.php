<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\ReporteProblema;
use App\Models\Zonas;
use App\Models\ZonasPoligono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZonasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index(){
        return view('backend.admin.configuracion.zonas.vistazonas');
    }

    // tabla para ver zonas
    public function tablaZonas(){
        $zonas = Zonas::orderBy('id', 'ASC')->get();

        foreach ($zonas as $info){

            $info->hora_abierto_delivery = date("h:i A", strtotime($info->hora_abierto_delivery));
            $info->hora_cerrado_delivery = date("h:i A", strtotime($info->hora_cerrado_delivery));

            $info->minimo = '$' . number_format((float)$info->minimo, 2, '.', ',');
        }


        return view('backend.admin.configuracion.zonas.tablazonas', compact('zonas'));
    }

    // crear zona
    public function nuevaZona(Request $request){

        $rules = array(
            'nombre' => 'required',
            'latitud' => 'required',
            'longitud' => 'required',
            'minimo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        $zona = new Zonas();
        $zona->nombre = $request->nombre;
        $zona->latitud = $request->latitud;
        $zona->longitud = $request->longitud;
        $zona->saturacion = 0;
        $zona->hora_abierto_delivery = $request->horaabierto;
        $zona->hora_cerrado_delivery = $request->horacerrado;
        $zona->activo = 0;
        $zona->tiempo_extra = $request->tiempoextra;
        $zona->mensaje_bloqueo = null;
        $zona->minimo = $request->minimo;
        $zona->descripcion = $request->descripcion;

        if($zona->save()){
            return ['success'=>1];
        }else{
            return ['success'=>2];
        }
    }

    // informacion de la zona
    public function informacionZona(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if($zona = Zonas::where('id', $request->id)->first()){

            return['success' => 1, 'zona' => $zona];
        }else{
            return['success' => 2];
        }

    }

    // editar la zona
    public function editarZona(Request $request){
        $rules = array(
            'id' => 'required',
            'nombre' => 'required',
            'togglep' => 'required',
            'togglea' => 'required',
            'latitud' => 'required',
            'longitud' => 'required',
            'minimo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if(Zonas::where('id', $request->id)->first()){

            if($request->togglea == 1){
                if(!ZonasPoligono::where('id_zonas', $request->id)->first()){
                    // no puede activar porque no tiene poligonos
                    return ['success' => 2];
                }
            }

            // actualizar zona

            Zonas::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'saturacion' => $request->togglep,
                'hora_abierto_delivery' => $request->horaabierto,
                'hora_cerrado_delivery' => $request->horacerrado,
                'activo' => $request->togglea,
                'tiempo_extra' => $request->tiempoextra,
                'mensaje_bloqueo' => $request->mensaje,
                'minimo' => $request->minimo,
                'descripcion' => $request->descripcion
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }

    public function indexPoligono($id){

        $nombre = Zonas::where('id', $id)->pluck('nombre')->first();

        return view('backend.admin.configuracion.zonas.poligonos.vistapoligonos', compact('nombre', 'id'));
    }

    public function nuevoPoligono(Request $request){

        $regla = array(
            'id' => 'required',
            'latitud' => 'required|array',
            'longitud' => 'required|array',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        for ($i = 0; $i < count($request->latitud); $i++) {

            $ingreso = new ZonasPoligono();
            $ingreso->id_zonas = $request->id;
            $ingreso->latitud = $request->latitud[$i];
            $ingreso->longitud = $request->longitud[$i];
            $ingreso->save();
        }

        return ['success' => 1];
    }

    public function borrarPoligonos(Request $request){

        $rules = array(
            'id' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        ZonasPoligono::where('id_zonas', $request->id)->delete();

        return ['success'=> 1];
    }

    public function verMapa($id){

        $googleapi = config('googleapi.Google_API');

        $poligono = ZonasPoligono::where('id_zonas', $id)->get();

        $infoZona = Zonas::where('id', $id)->first();

        $latitud = $infoZona->latitud;
        $longitud = $infoZona->longitud;

        return view('backend.admin.configuracion.zonas.mapa.vistamapa', compact('poligono', 'googleapi', 'latitud', 'longitud'));
    }













    //*******************************************************




    public function vistaProblemasAplicacion(){


        return view('backend.admin.configuracion.problemasapp.vistaproblemaapp');
    }



    public function tablaProblemasAplicacion(){

        $listado = ReporteProblema::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $dato){

            $infoCliente = Clientes::where('id', $dato->id_cliente)->first();


            $dato->usuariocliente = $infoCliente->usuario;


        }

        return view('backend.admin.configuracion.problemasapp.tablaproblemaapp', compact('listado'));
    }



    public function borrarFilaAppFallo(Request $request){


        $reglaDatos = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        ReporteProblema::where('id', $request->id)->delete();


        return ['success' => 1];
    }








}
