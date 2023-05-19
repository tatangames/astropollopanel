<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;



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



            $fechaRegistro = Carbon::parse($info->fecha);

            $tiempoSumado = $fechaRegistro->addMinute(15)->format('Y-m-d H:i:s');
            $fechaHoy = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');


            $d1 = new DateTime($tiempoSumado);
            $d2 = new DateTime($fechaHoy);

            if ($d1 > $d2){
                // PUEDE BORRAR REGISTRO

                $info->puedeborrar = 1;
            }else {
                // YA NO PUEDE BORRAR REGISTRO
                $info->puedeborrar = 0;
            }

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

        $fecha = Carbon::now('America/El_Salvador');

        $ca = new ZonasServicio();
        $ca->id_zonas = $request->zonaservicio;
        $ca->id_servicios = $request->servicio;
        $ca->fecha = $fecha;

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
