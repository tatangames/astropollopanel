<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\CarritoCallCenterExtra;
use App\Models\CarritoCallCenterTemporal;
use App\Models\DireccionesCallCenter;
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

        return view('backend.admin.callcenter.direcciones.vistaeditardireccion');
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


}
