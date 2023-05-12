<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZonasServicioController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $zonas = Zonas::orderBy('nombre')->get();
        $servicios = Servicios::orderBy('nombre')->get();

        return view('backend.admin.configuracion.zonasservicio.vistazonasservicio', compact('zonas', 'servicios'));
    }

    // tabla
    public function zonasServicioTablas(){

        $listado = ZonasServicio::orderBy('id', 'ASC')->get();

        foreach ($listado as $info){

            $infoZona = Zonas::where('id', $info->id_zonas)->first();
            $infoServicio = Servicios::where('id', $info->id_servicios)->first();

            $info->nombrezona = $infoZona->nombre;
            $info->nombrenegocio = $infoServicio->nombre;
        }

        return view('backend.admin.configuracion.zonasservicio.tablazonasservicio', compact('listado'));
    }


    public function nuevaZonaServicio(Request $request){

        $regla = array(
            'zonaservicio' => 'required',
            'servicio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        // No tiene que haber ninguna zona para guardar el registro
        if(ZonasServicio::where('id_zonas', $request->zonaservicio)->first()){
            return ['success' => 1];
        }

        $ca = new ZonasServicio();
        $ca->id_zonas = $request->zonaservicio;
        $ca->id_servicios = $request->servicio;

        if($ca->save()){
            return ['success' => 2];
        }else {
            return ['success' => 99];
        }
    }


    public function borrarRegistro(Request $request){

        $rules = array(
            'id' => 'required' // id (zonas)
        );
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        if(ZonasServicio::where('id', $request->id)->first()){
            ZonasServicio::where('id', $request->id)->delete();
        }

        return ['success'=> 1];
    }


}
