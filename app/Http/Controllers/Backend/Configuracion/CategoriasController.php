<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\CategoriasPrincipales;
use App\Models\Populares;
use App\Models\Productos;
use App\Models\SubCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $categorias = Categorias::where('id_servicios', $id)->orderBy('posicion')->get();

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

        if($infoCategoria = Categorias::where('id', $request->id)->first()){

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
                    $imagenOld = $infoCategoria->imagen;

                    Categorias::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'activo' => $request->cbactivo,
                        'usa_horario' => $request->cbhorario,
                        'hora_abre' => $request->horaabre,
                        'hora_cierra' => $request->horacierra,
                        'imagen' => $nombreFoto
                    ]);

                    if(Storage::disk('imagenes')->exists($imagenOld)){
                        Storage::disk('imagenes')->delete($imagenOld);
                    }

                    return ['success' => 1];

                }else{
                    return ['success' => 2];
                }
            }else{


                Categorias::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'activo' => $request->cbactivo,
                    'usa_horario' => $request->cbhorario,
                    'hora_abre' => $request->horaabre,
                    'hora_cierra' => $request->horacierra
                ]);


                return ['success' => 1];
            }







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









    //***************** SUB CATEGORIAS  ************************


    public function indexSubCategorias($id){
        // viene id categoria

        $infoCategoria = Categorias::where('id', $id)->first();
        $nomcategoria = $infoCategoria->nombre;

        return view('backend.admin.configuracion.categorias.subcategorias.vistasubcategorias', compact('id', 'nomcategoria'));
    }

    // tabla lista categorias
    public function subCategoriasTabla($id){

        $lista = SubCategorias::where('id_categorias', $id)->orderBy('posicion')->get();

        return view('backend.admin.configuracion.categorias.subcategorias.tablasubcategorias', compact('lista'));
    }

    public function nuevaSubCategorias(Request $request){

        $regla = array(
            'nombre' => 'required',
            'id' => 'required' // id de categoria
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = SubCategorias::where('id_categorias', $request->id)->orderBy('posicion', 'DESC')->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

        $subcategoria = new SubCategorias();
        $subcategoria->id_categorias = $request->id;
        $subcategoria->nombre = $request->nombre;
        $subcategoria->activo = 0;
        $subcategoria->posicion = $suma;

        if($subcategoria->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionSubCategorias(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($subCategoria = SubCategorias::where('id', $request->id)->first()){

            return ['success' => 1, 'subcategoria' => $subCategoria];
        }else{
            return ['success' => 2];
        }
    }

    public function editarSubCategorias(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(SubCategorias::where('id', $request->id)->first()){

            $verificar = true;

            // verificar que haya productos activos para activar esta categoria
            if($request->cbactivo == 1){
                if(Productos::where('id_subcategorias', $request->id)
                    ->where('activo', 1)
                    ->first()){
                    // encontro
                    $verificar = false;
                }

                // NO HAY PRODUCTOS EN SUB CATEGORIAS, NO SE PUEDE ACTIVAR
                if($verificar){
                    return ['success' => 1];
                }
            }


            SubCategorias::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'activo' => $request->cbactivo
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }


    public function ordenarSubCategorias(Request $request){

        $tasks = SubCategorias::all();

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




    // ****************** CATEGORIAS PRINCIPALES DEL SERVICIO  **************************


    public function indexServiciosCuponDescuentoDinero($id){

        // viene id de servicio

        $categorias = Categorias::where('id_servicios', $id)->orderBy('nombre')->get();
        return view('backend.admin.configuracion.servicios.categoriaprincipales.vistacategoriasprincipales', compact('categorias', 'id'));
    }

    // tabla
    public function tablaServiciosCuponDescuentoDinero($id){

        $listado = CategoriasPrincipales::where('id_servicios', $id)
        ->orderBy('posicion', 'ASC')
            ->get();

        foreach ($listado as $info){

            $infoCategoria = Categorias::where('id', $info->id_categorias)->first();
            $info->nomcategoria = $infoCategoria->nombre;
        }

        return view('backend.admin.configuracion.servicios.categoriaprincipales.tablacategoriasprincipales', compact('listado'));
    }


    public function nuevoCategoriaPrincipal(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'idcategoria' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // YA CATEGORIA ESTA REGISTRADA
        if(CategoriasPrincipales::where('id_servicios', $request->idservicio)
            ->where('id_categorias', $request->idcategoria)
            ->first()){
            return ['success' => 1];
        }

        if($info = CategoriasPrincipales::where('id_servicios', $request->idservicio)
            ->orderBy('posicion', 'DESC')
            ->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

        $ca = new CategoriasPrincipales();
        $ca->id_servicios = $request->idservicio;
        $ca->id_categorias = $request->idcategoria;
        $ca->posicion = $suma;

        if($ca->save()){
            return ['success' => 2];
        }else {
            return ['success' => 99];
        }
    }


    public function borrarCategoriaPrincipal(Request $request){

        $rules = array(
            'id' => 'required' // id (zonas)
        );
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        if(CategoriasPrincipales::where('id', $request->id)->first()){
            CategoriasPrincipales::where('id', $request->id)->delete();
        }

        return ['success'=> 1];
    }


    public function ordenarCategoriaPrincipal(Request $request){

        $tasks = CategoriasPrincipales::all();

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




    // ******************** PRODUCTOS PRINCIPALES DEL SERVICIO /////


    public function indexServiciosProductosPrincipales($id){

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


        return view('backend.admin.configuracion.servicios.productosprincipales.vistaproductosprincipales', compact('id', 'arrayProductos'));
    }

    public function tablaServiciosProductosPrincipales($id){

        $listado = Populares::where('id_servicios', $id)->orderBy('posicion')->get();

        foreach ($listado as $info){

            $infoProducto = Productos::where('id', $info->id_productos)->first();

            $info->nombre = $infoProducto->nombre;
            $info->activo = $infoProducto->activo;
            $info->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', ',');
            $info->imagen = $infoProducto->imagen;
            $info->utiliza_imagen = $infoProducto->utiliza_imagen;
        }

        return view('backend.admin.configuracion.servicios.productosprincipales.tablaproductosprincipales', compact('listado'));
    }


    public function nuevoProductosPrincipales(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'idproducto' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        // YA ESTA REGISTRADO EL PRODUCTO
        if(Populares::where('id_servicios', $request->idservicio)
            ->where('id_productos', $request->idproducto)
            ->first()){
            return ['success' => 1];
        }

        if($info = Populares::where('id_servicios', $request->idservicio)
            ->orderBy('posicion', 'DESC')
            ->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }

        $ca = new Populares();
        $ca->id_servicios = $request->idservicio;
        $ca->id_productos = $request->idproducto;
        $ca->posicion = $suma;

        if($ca->save()){
            return ['success' => 2];
        }else {
            return ['success' => 99];
        }
    }


    public function borrarProductosPrincipales(Request $request){

        $rules = array(
            'id' => 'required' // id (zonas)
        );
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        if(Populares::where('id', $request->id)->first()){
            Populares::where('id', $request->id)->delete();
        }

        return ['success'=> 1];

    }


    public function ordenarProductosPopulares(Request $request){

        $tasks = Populares::all();

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
