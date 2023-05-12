<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoriasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index($id){
        return view('backend.admin.configuracion.categorias.vistacategorias', compact('id'));
    }

    // tabla lista categorias
    public function categoriasTabla($id){

        $categorias = Categorias::where('id_servicios', $id)->orderBy('nombre')->get();

        foreach ($categorias as $info){

            $info->hora_abre = date("h:i A", strtotime($info->hora_abre));
            $info->hora_cierra = date("h:i A", strtotime($info->hora_cierra));
        }

        return view('backend.admin.configuracion.categorias.tablacategorias', compact('categorias'));
    }

    public function nuevaCategorias(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Categorias::where('id_servicios', $request->id)->orderBy('posicion', 'DESC')->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

        $cadena = Str::random(15);
        $tiempo = microtime();
        $union = $cadena.$tiempo;
        $nombre = str_replace(' ', '_', $union);

        $extension = '.'.$request->imagen->getClientOriginalExtension();
        $nombreFoto = $nombre.strtolower($extension);
        $avatar = $request->file('imagen');
        $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));


        if($upload){
            $ca = new Categorias();
            $ca->id_servicios = $request->id;
            $ca->nombre = $request->nombre;
            $ca->activo = 0;
            $ca->posicion = $suma;
            $ca->usa_horario = $request->toggle;
            $ca->hora_abre = $request->horaabre;
            $ca->hora_cierra = $request->horacierra;
            $ca->imagen = $nombreFoto;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }

    }

    public function informacionCategorias(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($categoria = Categorias::where('id', $request->id)->first()){

            return ['success' => 1, 'categoria' => $categoria];
        }else{
            return ['success' => 2];
        }
    }

    public function editarCategorias(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Categorias::where('id', $request->id)->first()){

            $verificar = true;

            // verificar que haya productos activos para activar esta categoria
            if($request->cbactivo == 1){
                if(Productos::where('id_categorias', $request->id)
                    ->where('activo', 1)->first()){
                    // encontro
                    $verificar = false;
                }
            }

            if($verificar){
                return ['success' => 1];
            }

            Categorias::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'activo' => $request->cbactivo,
                'usa_horario' => $request->cbhorario,
                'hora_abre' => $request->horaabre,
                'hora_cierra' => $request->horacierra
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }


    public function ordenarCategorias(Request $request){

        $tasks = Categorias::all();

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



}
