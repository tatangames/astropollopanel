<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\CuponDescuentoDinero;
use App\Models\CuponDescuentoPorcentaje;
use App\Models\Cupones;
use App\Models\CuponProductoGratis;
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


    // *****************************************************

    public function indexServiciosCuponProGratis($id){
        // viene id servicios

        // listado de nombre cupon que fueron asignados como producto gratis
        $lista = Cupones::where('id_tipo_cupon', 1)->orderBy('texto_cupon')->get();

        return view('backend.admin.configuracion.servicios.cupones.producto.vistacuponproducto', compact('id', 'lista'));
    }


    public function tablaServiciosCuponProGratis($id){

        $lista = CuponProductoGratis::where('id_servicios', $id)->orderBy('nombre')->get();

        foreach ($lista as $info){

            $infoCupon = Cupones::where('id', $info->id_cupones)->first();
            $info->nombrecupon = $infoCupon->texto_cupon;
        }

        return view('backend.admin.configuracion.servicios.cupones.producto.tablacuponproducto', compact('lista'));
    }

    public function nuevoCuponProGratis(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'idcupon' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // REGLA: NO CUPONES REPETIDOS

        if(CuponProductoGratis::where('id_cupones', $request->idcupon)
            ->where('id_servicios', $request->idservicio)
            ->first()){
            return ['success' => 1];
        }

        $cupon = new CuponProductoGratis();
        $cupon->id_cupones = $request->idcupon;
        $cupon->id_servicios = $request->idservicio;
        $cupon->nombre = $request->nombre;

        if($cupon->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }


    public function borrarCuponProGratis(Request $request){

        if (CuponProductoGratis::where('id', $request->id)->first()) {
            CuponProductoGratis::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }







    // *****************************************************

    public function indexServiciosCuponDescuentoDinero($id){
        // viene id servicios

        // listado de nombre cupon que fueron asignados como producto dinero
        $lista = Cupones::where('id_tipo_cupon', 2)->orderBy('texto_cupon')->get();

        return view('backend.admin.configuracion.servicios.cupones.dinero.vistacupondinero', compact('id', 'lista'));
    }


    public function tablaServiciosCuponDescuentoDinero($id){

        $lista = CuponDescuentoDinero::where('id_servicios', $id)->orderBy('dinero')->get();

        foreach ($lista as $info){

            $infoCupon = Cupones::where('id', $info->id_cupones)->first();
            $info->nombrecupon = $infoCupon->texto_cupon;

            $info->dinero = '$' . number_format((float)$info->dinero, 2, '.', ',');

        }

        return view('backend.admin.configuracion.servicios.cupones.dinero.tablacupondinero', compact('lista'));
    }




    public function nuevoCuponDescuentoDinero(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'idcupon' => 'required',
            'monto' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // REGLA: NO CUPONES REPETIDOS

        if(CuponDescuentoDinero::where('id_cupones', $request->idcupon)
            ->where('id_servicios', $request->idservicio)
            ->first()){
            return ['success' => 1];
        }

        $cupon = new CuponDescuentoDinero();
        $cupon->id_cupones = $request->idcupon;
        $cupon->id_servicios = $request->idservicio;
        $cupon->dinero = $request->monto;

        if($cupon->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }


    public function borrarCuponDescuentoDinero(Request $request){

        if (CuponDescuentoDinero::where('id', $request->id)->first()) {
            CuponDescuentoDinero::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }




    // *****************************************************

    public function indexServiciosCuponDescuentoPorcentaje($id){
        // viene id servicios

        // listado de nombre cupon que fueron asignados como descuento porcentaje
        $lista = Cupones::where('id_tipo_cupon', 3)->orderBy('texto_cupon')->get();

        return view('backend.admin.configuracion.servicios.cupones.porcentaje.vistacuponporcentaje', compact('id', 'lista'));
    }


    public function tablaServiciosCuponDescuentoPorcentaje($id){

        $lista = CuponDescuentoPorcentaje::where('id_servicios', $id)->orderBy('porcentaje')->get();

        foreach ($lista as $info){

            $infoCupon = Cupones::where('id', $info->id_cupones)->first();
            $info->nombrecupon = $infoCupon->texto_cupon;
        }

        return view('backend.admin.configuracion.servicios.cupones.porcentaje.tablacuponporcentaje', compact('lista'));
    }




    public function nuevoCuponDescuentoPorcentaje(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'idcupon' => 'required',
            'porcentaje' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // REGLA: NO CUPONES REPETIDOS

        if(CuponDescuentoPorcentaje::where('id_cupones', $request->idcupon)
            ->where('id_servicios', $request->idservicio)
            ->first()){
            return ['success' => 1];
        }

        $cupon = new CuponDescuentoPorcentaje();
        $cupon->id_cupones = $request->idcupon;
        $cupon->id_servicios = $request->idservicio;
        $cupon->porcentaje = $request->porcentaje;

        if($cupon->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }


    public function borrarCuponDescuentoPorcentaje(Request $request){

        if (CuponDescuentoPorcentaje::where('id', $request->id)->first()) {
            CuponDescuentoPorcentaje::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }





}
