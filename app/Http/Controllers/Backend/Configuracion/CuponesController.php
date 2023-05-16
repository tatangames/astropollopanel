<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Cupones;
use App\Models\TipoCupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuponesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index(){

        // no quiero las zonas que ya han sido utilizadas
        $listaCupones = TipoCupon::orderBy('id', 'ASC')->get();

        return view('backend.admin.configuracion.cupones.vistacupones', compact('listaCupones'));
    }

    // tabla
    public function cuponesTabla(){

        $cupones = Cupones::orderBy('texto_cupon')->get();

        foreach ($cupones as $info){

            $infoTipo = TipoCupon::where('id', $info->id_tipo_cupon)->first();
            $info->tipocupon = $infoTipo->nombre;
        }

        return view('backend.admin.configuracion.cupones.tablacupones', compact('cupones'));
    }


    public function nuevoRegistro(Request $request){

        $regla = array(
            'tipocupon' => 'required',
            'nombre' => 'required',
            'limite' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // REGLA: NO CUPONES REPETIDOS

        if(Cupones::where('texto_cupon', $request->nombre)->first()){
            return ['success' => 1];
        }

        $cupon = new Cupones();
        $cupon->id_tipo_cupon = $request->tipocupon;
        $cupon->texto_cupon = $request->nombre;
        $cupon->uso_limite = $request->limite;
        $cupon->contador = 0;
        $cupon->activo = 1;

        if($cupon->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }


    public function informacionCupon(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Cupones::where('id', $request->id)->first()){

            return ['success' => 1, 'cupones' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarCupon(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required',
            'limite' => 'required',
            'activo' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}


        if(Cupones::where('texto_cupon', $request->nombre)
            ->where('id', '!=', $request->id)
            ->first()){
            return ['success' => 1];
        }

        if(Cupones::where('id', $request->id)->first()){

            Cupones::where('id', $request->id)->update([
                'texto_cupon' => $request->nombre,
                'uso_limite' => $request->limite,
                'activo' => $request->activo
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }



}
