<?php

namespace App\Http\Controllers\Backend\Premios;

use App\Http\Controllers\Controller;
use App\Models\ClientesPremios;
use App\Models\Premios;
use App\Models\Servicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PremiosController extends Controller
{

    public function indexListaPremios($idservicio){

        $infoServicio = Servicios::where('id', $idservicio)->first();

        $nombre = $infoServicio->nombre;

        return view('backend.admin.configuracion.servicios.premios.vistapremios', compact('idservicio', 'nombre'));
    }


    public function tablaListaPremios($idservicio){

        $listado = Premios::where('id_servicio', $idservicio)->get();


        return view('backend.admin.configuracion.servicios.premios.tablapremios', compact('listado'));
    }


    public function registrarNuevoPremio(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'nombre' => 'required',
            'puntos' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        $dato = new Premios();
        $dato->id_servicio = $request->idservicio;
        $dato->nombre = $request->nombre;
        $dato->puntos = $request->puntos;
        $dato->activo = 0;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionPremios(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Premios::where('id', $request->id)->first()){


            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 99];
        }
    }


    public function actualizarPremio(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'puntos' => 'required',
            'toggle' => 'required'
        );

        // password

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        DB::beginTransaction();

        try {

            if($request->toggle == 0){
                // VAMOS A ELIMINAR TODOS LOS CLIENTES QUE TENGAN SELECCIONADO ESTE PREMIO

                ClientesPremios::where('id_premios', $request->id)->delete();
            }

            Premios::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'puntos' => $request->puntos,
                'activo' => $request->toggle
            ]);


            DB::commit();

            return ['success' => 1];

        } catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }






}
