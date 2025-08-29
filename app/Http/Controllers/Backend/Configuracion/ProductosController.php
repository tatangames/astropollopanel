<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\Productos;
use App\Models\SubCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductosController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index($id){

        $subcategoria = SubCategorias::where('id', $id)->first();
        $nomsubcategoria = $subcategoria->nombre;

        return view('backend.admin.configuracion.productos.vistaproductos', compact('id', 'nomsubcategoria'));
    }


    // tabla de productos
    public function productosTabla($id){

        $productos = Productos::where('id_subcategorias', $id)
            ->orderBy('posicion')
            ->get();

        foreach ($productos as $pp){

            $pp->precio = number_format((float)$pp->precio, 2, '.', ',');
        }

        return view('backend.admin.configuracion.productos.tablaproductos', compact('productos'));
    }


    public function nuevoProducto(Request $request){

        $regla = array(
            'nombre' => 'required',
            'idsubcategoria' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Productos::where('id_subcategorias', $request->idsubcategoria)
            ->orderBy('posicion', 'DESC')->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

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
                $ca = new Productos();
                $ca->id_subcategorias = $request->idsubcategoria;
                $ca->nombre = $request->nombre;
                $ca->imagen = $nombreFoto;
                $ca->descripcion = $request->descripcion;
                $ca->precio = $request->precio;
                $ca->activo = 1;
                $ca->posicion = $suma;
                $ca->utiliza_nota = $request->cbnota;
                $ca->nota = $request->nota;
                $ca->utiliza_imagen = $request->cbimagen;

                if($ca->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }else{
                return ['success' => 2];
            }
        }else {

            $ca = new Productos();
            $ca->id_subcategorias = $request->idsubcategoria;
            $ca->nombre = $request->nombre;
            $ca->descripcion = $request->descripcion;
            $ca->precio = $request->precio;
            $ca->activo = 1;
            $ca->imagen = null;
            $ca->posicion = $suma;
            $ca->utiliza_nota = $request->cbnota;
            $ca->nota = $request->nota;
            $ca->utiliza_imagen = 0;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function informacionProductos(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Productos::where('id', $request->id)->first()){

            $infoSubCate = SubCategorias::where('id', $info->id_subcategorias)->first();

            // listado de sub categorias para mover un producto
            $arraySubCate = SubCategorias::where('id_categorias', $infoSubCate->id_categorias)->get();

            return ['success' => 1, 'producto' => $info, 'arraysub' => $arraySubCate];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProductos(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required',
            'idsubcate' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Productos::where('id', $request->id)->first()){

            if($request->hasFile('imagen')){

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen');
                $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                if($upload){
                    $imagenOld = $info->imagen;

                    Productos::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'precio' => $request->precio,
                        'activo' => $request->cbactivo,
                        'utiliza_nota' => $request->cbnota,
                        'nota' => $request->nota,
                        'utiliza_imagen' => $request->cbimagen,
                        'imagen' => $nombreFoto,
                        'id_subcategorias' => $request->idsubcate
                    ]);

                    if(Storage::disk('imagenes')->exists($imagenOld)){
                        Storage::disk('imagenes')->delete($imagenOld);
                    }

                    return ['success' => 1];

                }else{
                    return ['success' => 2];
                }
            }else{
                // solo guardar datos

                if($info->imagen == null){
                    if($request->cbimagen == 1){
                        // quiere utilizar imagen pero no hay
                        return ['success' => 3];
                    }
                }

                Productos::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'precio' => $request->precio,
                    'activo' => $request->cbactivo,
                    'utiliza_nota' => $request->cbnota,
                    'nota' => $request->nota,
                    'utiliza_imagen' => $request->cbimagen,
                    'id_subcategorias' => $request->idsubcate
                ]);

                return ['success' => 1];
            }

        }else{
            return ['success' => 2];
        }
    }

    public function ordenarProductos(Request $request){

        $tasks = Productos::all();

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
