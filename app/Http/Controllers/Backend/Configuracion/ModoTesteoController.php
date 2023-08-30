<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\Productos;
use App\Models\ProductosModoTesteo;
use App\Models\Servicios;
use App\Models\SubCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModoTesteoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }



    public function vistaListadoProductosTesteo($idservicio){

        // INFORMACION SI BOTON ESTARA ACTIVADO PARA MODO TESTEO

        $infoServicio = Servicios::where('id', $idservicio)->first();

        // LISTADO DE PRODUCTOS PARA MOSTRAR EN EL MODO PRUEBA
        $arrayCategorias = Categorias::where('id_servicios', $idservicio)->get();

        $pilaCategorias = array();

        foreach ($arrayCategorias as $info){
            array_push($pilaCategorias, $info->id);
        }


        //*****

        $arraySubcategoria = SubCategorias::whereIn('id_categorias', $pilaCategorias)->get();

        $pilaSubCategorias = array();

        foreach ($arraySubcategoria as $info){
            array_push($pilaSubCategorias, $info->id);
        }


        // ****

        $arrayPro = Productos::whereIn('id_subcategorias', $pilaSubCategorias)
            ->orderBy('nombre', 'ASC')
            ->get();


        return view('backend.admin.modotesteo.productos.vistaproductos', compact('idservicio', 'infoServicio', 'arrayPro'));
    }


    public function tablaListadoProductosTesteo($idservicio){

        $listado = ProductosModoTesteo::where('id_servicios', $idservicio)
            ->orderBy('posicion')
            ->get();

        foreach ($listado as $dato){

            $infoProducto = Productos::where('id', $dato->id_producto)->first();


            $dato->nombrepro = $infoProducto->nombre;
            $dato->precio = $infoProducto->precio;
            $dato->utiliza_imagen = $infoProducto->utiliza_imagen;
            $dato->imagen = $infoProducto->imagen;
        }

        return view('backend.admin.modotesteo.productos.tablaproductos', compact('listado'));
    }


    public function ordenarProductosModoTesteo(Request $request){

        $tasks = ProductosModoTesteo::all();

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



    public function nuevoProductosModoTesteo(Request $request){


        $regla = array(
            'idservicio' => 'required',
            'idproducto' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = ProductosModoTesteo::where('id_servicios', $request->idservicio)
                ->orderBy('posicion', 'DESC')
                ->first()){
            $suma = $info->posicion + 1;
        }else{
            $suma = 1;
        }


        $dato = new ProductosModoTesteo();
        $dato->id_servicios = $request->idservicio;
        $dato->id_producto = $request->idproducto;
        $dato->posicion = $suma;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }




    public function borrarProductosModoTesteo(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        if($info = ProductosModoTesteo::where('id', $request->id)->first()){

            ProductosModoTesteo::where('id', $info->id)->delete();

            return ['success' => 1];

        }else{
            // siempre regresar que fue borrada
            return ['success' => 1];
        }
    }



    public function modificarToggleModoTesteo(Request $request){


        $regla = array(
            'idservicio' => 'required',
            'valor' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if(Servicios::where('id', $request->idservicio)->first()){

            Servicios::where('id', $request->idservicio)
                ->update(['modo_prueba' => $request->valor]);


            return ['success' => 1];

        }else{
            return ['success' => 99];
        }


    }



}
