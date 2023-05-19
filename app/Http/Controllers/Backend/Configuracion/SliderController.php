<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\Productos;
use App\Models\Slider;
use App\Models\SubCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index($id){
        // id servicios

        $arrayCategorias = Categorias::where('id_servicios', $id)->get();
        $pilaIdCategorias = array();

        foreach ($arrayCategorias as $info){
            array_push($pilaIdCategorias, $info->id);
        }


        // obtener todos los ID de sub categorias
        $arraySubCate = SubCategorias::whereIn('id_categorias', $pilaIdCategorias)->get();

        $pilaIdSubCategorias = array();

        foreach ($arraySubCate as $info){
            array_push($pilaIdSubCategorias, $info->id);
        }

        // obtener todos los productos de las sub categorias

        $arrayProductos = Productos::whereIn('id_subcategorias', $pilaIdSubCategorias)->get();


        return view('backend.admin.configuracion.slider.vistaslider', compact('arrayProductos', 'id'));
    }

    public function sliderTabla($id){
        // id servicios

        $slider = Slider::orderBy('posicion')->get();

        foreach ($slider as $info){

            if($infoProducto = Productos::where('id', $info->id_producto)->first()){
                $info->producto = $infoProducto->nombre;
            }

            $info->hora_abre = date("h:i A", strtotime($info->hora_abre));
            $info->hora_cierra = date("h:i A", strtotime($info->hora_cierra));
        }

        return view('backend.admin.configuracion.slider.tablaslider', compact('slider'));
    }

    public function nuevaSlider(Request $request){

        // idservicio

        if($request->file('imagen')){

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen');
            $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

            if($upload){

                if($info = Slider::where('id_servicios', $request->idservicio)
                    ->orderBy('posicion', 'DESC')
                    ->first()){
                    $suma = $info->posicion + 1;
                }else{
                    $suma = 1;
                }

                $ca = new Slider();
                $ca->id_producto = $request->producto;
                $ca->id_servicios = $request->idservicio;
                $ca->imagen = $nombreFoto;
                $ca->posicion = $suma;
                $ca->nombre = $request->nombre;
                $ca->redireccionamiento = $request->toggledireccion;
                $ca->usa_horario = $request->togglehorario;
                $ca->hora_abre = $request->horaabre;
                $ca->hora_cierra = $request->horacierra;
                $ca->activo = 1;

                if($ca->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 99];
                }
            }else{
                return ['success' => 99];
            }

        }else {
            return ['success' => 99];
        }
    }

    function editarSlider(Request $request){

        $rules = array(
            'idslider' => 'required',
            'toggleactivo' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if(Slider::where('id', $request->idslider)->first()){

            Slider::where('id', $request->idslider)->update([
                'activo' => $request->toggleactivo,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
    public function ordenarSliders(Request $request){
        $tasks = Slider::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }

    public function borrarSliders(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Slider::where('id', $request->id)->first()){

            $conteo = Slider::where('id_servicios', $info->id_servicios)
                ->where('id', '!=', $info->id)
                ->count();

            if($conteo <= 0){
                return ['success' => 1];
            }

            if(Storage::disk('imagenes')->exists($info->imagen)){
                Storage::disk('imagenes')->delete($info->imagen);
            }

            Slider::where('id', $request->id)->delete();

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    public function informacionSlider(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($bloque = Slider::where('id', $request->id)->first()){

            return ['success' => 1, 'slider' => $bloque];
        }else{
            return ['success' => 2];
        }
    }

}
