<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use App\Models\ClienteModoTesteo;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Productos;
use App\Models\ProductosModoTesteo;
use App\Models\ProductoTesteoCarrito;
use App\Models\Servicios;
use App\Models\Slider;
use App\Models\SubCategorias;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiMenuController extends Controller
{


    public function listadoMenuPrincipal(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($data = Clientes::where('id', $request->id)->first()){
            if($data->activo == 0){
                $titulo = "Nota";
                $mensaje = "Usuario ha sido bloqueado.";

                // bloquear usuario
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }
        }

        // conocer si tiene direccion el cliente
        $infoConteoDireccion = DireccionCliente::where('id_cliente', $request->id)->count();

        if($infoConteoDireccion == 0){
            $mensaje = "No hay direccion de entrega";
            return ['success' => 2, 'mensaje' => $mensaje];
        }

        // el cliente si tiene una direccion seleccionada
        if($infoDireccion = DireccionCliente::where('id_cliente', $request->id)
            ->where('seleccionado', 1)
            ->first()){

            // buscar si hay un servicio asignado a la zona
            if($infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireccion->id_zonas)->first()){

                $slider = Slider::where('id_servicios', $infoZonaServicio->id_servicios)
                    ->where('activo', 1)
                    ->orderBy('posicion')
                    ->get();

                // el 0 que se asigna, verifico que producto no lleva redireccionamiento en slider
                foreach ($slider as $info){

                    if($info->redireccionamiento == 0){
                        $info->id_producto = 0;
                    }

                    if($info->id_producto == null){
                        $info->id_producto = 0;
                    }
                }

                //**********************************

                $getValores = Carbon::now('America/El_Salvador');
                $hora = $getValores->format('H:i:s');

                $pilaIdCategorias = array();

                // obtener las categorias del servicio (ACTIVAS Y UTILIZAN HORARIO)
                 $categoriasHorario = DB::table('categorias_principales AS cp')
                    ->join('categorias AS c', 'cp.id_categorias', '=', 'c.id')
                    ->select('c.id', 'c.activo', 'c.id_servicios', 'c.usa_horario', 'c.hora_abre', 'c.hora_cierra')
                    ->where('c.id_servicios', $infoZonaServicio->id_servicios)
                    ->where('c.activo', 1)
                    ->where('c.usa_horario', 1)
                    ->where('c.hora_abre', '<=', $hora)
                    ->where('c.hora_cierra', '>=', $hora)
                    ->get();

                foreach ($categoriasHorario as $info){
                    array_push($pilaIdCategorias, $info->id);
                }

                // obtener las categorias del servicio (ACTIVAS)

                $categoriasActivas = DB::table('categorias_principales AS cp')
                    ->join('categorias AS c', 'cp.id_categorias', '=', 'c.id')
                    ->select('c.id', 'c.activo', 'c.id_servicios', )
                    ->where('c.id_servicios', $infoZonaServicio->id_servicios)
                    ->where('c.activo', 1)
                    ->where('c.usa_horario', 0)
                    ->get();

                foreach ($categoriasActivas as $info){
                    array_push($pilaIdCategorias, $info->id);
                }

                // listado de categorias ya filtradas
                $arrayCategorias = DB::table('categorias_principales AS cp')
                    ->join('categorias AS ca', 'cp.id_categorias', '=', 'ca.id')
                    ->select('ca.id', 'ca.imagen', 'ca.nombre', 'cp.posicion')
                    ->whereIn('ca.id', $pilaIdCategorias)
                    ->orderBy('cp.posicion', 'ASC')
                    ->get();

                // validar que haya categorias, sino ocultar
                $hayCategorias = 0;
                if ($arrayCategorias->count()){
                    $hayCategorias = 1;
                }


                //**********************

                $pilaIdPopulares = array();

                // obtener los productos donde su categoria lider este (ACTIVAS Y UTILIZAN HORARIO)

                $arrayPopularesHorario = DB::table('populares AS pop')
                    ->join('productos AS pro', 'pop.id_productos', '=', 'pro.id')
                    ->join('sub_categorias AS subca', 'pro.id_subcategorias', '=', 'subca.id')
                    ->join('categorias AS ca', 'subca.id_categorias', '=', 'ca.id')
                    ->select('ca.activo', 'pro.id', 'ca.usa_horario', 'ca.hora_abre', 'ca.hora_cierra', 'ca.id_servicios')
                    ->where('ca.id_servicios', $infoZonaServicio->id_servicios)
                    ->where('ca.activo', 1)
                    ->where('ca.usa_horario', 1)
                    ->where('ca.hora_abre', '<=', $hora)
                    ->where('ca.hora_cierra', '>=', $hora)
                    ->get();

                foreach ($arrayPopularesHorario as $info){
                    array_push($pilaIdPopulares, $info->id);
                }


                // obtener los productos donde su categoria lider este (ACTIVAS)

                $arrayPopulares = DB::table('populares AS pop')
                    ->join('productos AS pro', 'pop.id_productos', '=', 'pro.id')
                    ->join('sub_categorias AS subca', 'pro.id_subcategorias', '=', 'subca.id')
                    ->join('categorias AS ca', 'subca.id_categorias', '=', 'ca.id')
                    ->select('ca.activo', 'pro.id', 'ca.usa_horario', 'ca.hora_abre', 'ca.hora_cierra', 'ca.id_servicios')
                    ->where('ca.id_servicios', $infoZonaServicio->id_servicios)
                    ->where('ca.activo', 1)
                    ->get();

                foreach ($arrayPopulares as $info){
                    array_push($pilaIdPopulares, $info->id);
                }

                $arrayProductos = DB::table('populares AS pop')
                    ->join('productos AS pro', 'pop.id_productos', '=', 'pro.id')
                    ->select('pro.id', 'pro.nombre', 'pro.imagen', 'pro.utiliza_imagen', 'pro.precio', 'pop.posicion')
                    ->whereIn('pro.id', $pilaIdPopulares)
                    ->orderBy('pop.posicion')
                    ->get();

                foreach ($arrayProductos as $info){
                    $info->precio = '$' . number_format((float)$info->precio, 2, '.', ',');
                }

                $hayPopulares = 0;
                if ($arrayProductos->count()){
                    $hayPopulares = 1;
                }


                // MOSTRAR O NO EL BOTON DE MI PRIMERA ORDEN

                $infoRestaurante = Servicios::where('id', $infoZonaServicio->id_servicios)->first();

                if($infoRestaurante->modo_prueba == 0){
                    $btnServicioTesteo = 0;
                }else{
                    $btnServicioTesteo = 1;
                }

                    $btnClienteTesteo = 1;
                if(ClienteModoTesteo::where('id_cliente', $request->id)->first()){
                    // ya existe asi que ocultarlo
                    $btnClienteTesteo = 0;
                }



                // el slider siempre estara fijo en la app
                return [
                    'success' => 3,
                    'slider' => $slider,
                    'categorias' => $arrayCategorias,
                    'populares' => $arrayProductos,
                    'haycategorias' => $hayCategorias,
                    'haypopulares' => $hayPopulares,
                    'btntesteocliente' => $btnClienteTesteo,
                    'btntesteoservicio' => $btnServicioTesteo
                ];

            }
            else{
                $mensaje = "No hay un servicio asociado a la zona";
                return ['success' => 4, 'mensaje' => $mensaje];
            }
        }
        else{
            $mensaje = "No hay direccion de entrega seleccionado";
            return ['success' => 5, 'mensaje' => $mensaje];
        }
    }





    public function listaDeTodasLasCategorias(Request $request){

        // ESTA PETICION SIEMPRE EL CLIENTE DEBERA TENER UNA DIRECCION YA REGISTRADA

        $infoDireccion = DireccionCliente::where('id_cliente', $request->id)
            ->where('seleccionado', 1)
            ->first();

        if($infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireccion->id_zonas)->first()){

            $getValores = Carbon::now('America/El_Salvador');
            $hora = $getValores->format('H:i:s');

            $pilaIdCategorias = array();

            // obtener las categorias del servicio (ACTIVAS Y UTILIZAN HORARIO)
            $categoriasHorario = Categorias::where('id_servicios', $infoZonaServicio->id_servicios)
                ->where('activo', 1)
                ->where('usa_horario', 1)
                ->where('hora_abre', '<=', $hora)
                ->where('hora_cierra', '>=', $hora)
                ->get();

            foreach ($categoriasHorario as $info){
                array_push($pilaIdCategorias, $info->id);
            }

            // obtener las categorias del servicio (ACTIVAS)

            $categoriasActivas = Categorias::where('id_servicios', $infoZonaServicio->id_servicios)
                ->where('activo', 1)
                ->where('usa_horario', 0)
                ->get();

            foreach ($categoriasActivas as $info){
                array_push($pilaIdCategorias, $info->id);
            }

            // listado de categorias ya filtradas
            $arrayCategorias = Categorias::whereIn('id', $pilaIdCategorias)
                ->orderBy('posicion', 'ASC')
                ->get();


            return ['success' => 1, 'categorias' => $arrayCategorias];

        }else{
            $mensaje = "No hay una servicio asociado a la zona";
            return ['success' => 2, 'mensaje' => $mensaje];
        }
    }



    // retorna listado de productos cuando es seleccionada una categoria
    public function listaDeTodosLosProductosServicio(Request $request){

        // viene id categoria

        $reglaDatos = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        $arraySubcategorias = SubCategorias::where('id_categorias', $request->id)
                ->where('activo', 1)
                ->orderBy('posicion', 'ASC')
                ->get();

            $resultsBloque = array();
            $index = 0;

            foreach($arraySubcategorias as $secciones){
                array_push($resultsBloque,$secciones);

                $subSecciones = Productos::where('id_subcategorias', $secciones->id)
                    ->where('activo', 1)
                    ->orderBy('posicion', 'ASC')
                    ->get();

                $resultsBloque[$index]->productos = $subSecciones; //agregar los productos en la sub seccion
                $index++;
            }

            return [
                'success' => 1,
                'productos' => $arraySubcategorias,
            ];
        }


        public function informacionProductoIndividual(Request $request){

            $reglaDatos = array(
                'id' => 'required',   // id producto
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos);

            if($validarDatos->fails()){return ['success' => 0]; }

            if(Productos::where('id', $request->id)->first()){

                $producto = Productos::where('id', $request->id)->get();

                return ['success' => 1, 'producto' => $producto];

            }else{
                return ['success' => 2];
            }
        }




        //****************************

    public function listadoProductosTesteo(Request $request){

        $reglaDatos = array(
            'idcliente' => 'required',   // id cliente
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->idcliente)->first()){

            // obtener direccion actual

            $infoDireccion = DireccionCliente::where('id_cliente', $request->idcliente)
                ->where('seleccionado', 1)
                ->first();

            $infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireccion->id_zonas)->first();

            $idservicio = $infoZonaServicio->id_servicios;


            $listado = ProductosModoTesteo::where('id_servicios', $idservicio)
                ->orderBy('posicion', 'ASC')
                ->get();


            foreach ($listado as $dato){

                $infoProducto = Productos::where('id', $dato->id_producto)->first();

                $dato->nombrepro = $infoProducto->nombre;
                $dato->precio = '$' . number_format((float)$infoProducto->precio, 2, '.', '');
                $dato->imagen = $infoProducto->imagen;
                $dato->utiliza_imagen = $infoProducto->utiliza_imagen;
                $dato->descripcion = $infoProducto->descripcion;
                $dato->idpro = $infoProducto->id;
            }

            return ['success' => 1, 'listado' => $listado];

        }else{
            return ['success' => 2];
        }
    }


    public function infoProductoIndividualModoTesteo(Request $request){

        $reglaDatos = array(
            'idpro' => 'required',   // id producto
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Productos::where('id', $request->id)->first()){

            $producto = Productos::where('id', $request->id)->get();

            return ['success' => 1, 'producto' => $producto];

        }else{
            return ['success' => 2];
        }
    }



    public function agregarProductoCarritoTemporalModoTesteo(Request $request){

        $reglaDatos = array(
            'productoid' => 'required',
            'clienteid' => 'required',
            'cantidad' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }


        DB::beginTransaction();

        try {

            $extra = new ProductoTesteoCarrito();
            $extra->id_cliente = $request->clienteid;
            $extra->id_producto = $request->productoid;
            $extra->cantidad = $request->cantidad;
            $extra->save();

            DB::commit();

            // producto guardado
            return ['success' => 1];

        }catch(\Error $e){

            DB::rollback();

            return [
                'success' => 100
            ];
        }
    }





    public function verCarritoDecomprasModoTesteo(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );


        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->clienteid)->first()){

            try {


                $subTotal = 0;


                // preguntar si usuario ya tiene un carrito de compras
                if(ProductoTesteoCarrito::where('id_cliente', $request->clienteid)->first()){

                    $estadoProductoActivo = 1;

                    $producto = ProductoTesteoCarrito::where('id_cliente', $request->clienteid)->get();

                    $conteo = 0;

                    // verificar cada producto
                    foreach ($producto as $pro) {
                        $conteo++;

                        $infoProducto = Productos::where('id', $pro->id_producto)->first();

                        // multiplicar cantidad por el precio de cada producto
                        $precio = $pro->cantidad * $infoProducto->precio;

                        $subTotal = $subTotal + $precio;

                        // convertir
                        $pro->precioformat = '$' . number_format((float)$precio, 2, '.', ',');


                        $pro->imagen = $infoProducto->imagen;
                        $pro->utiliza_imagen = $infoProducto->utiliza_imagen;
                        $pro->nombre = $infoProducto->nombre;
                        $pro->descripcion = $infoProducto->descripcion;


                        if($pro->imagen == null){
                            $pro->imagen = "camaradefecto.png";
                        }
                    }

                    // sub total de la orden
                    $subTotal = number_format((float)$subTotal, 2, '.', ',');

                    return [
                        'success' => 1,
                        'subtotal' => $subTotal,
                        'estadoProductoGlobal' => $estadoProductoActivo,
                        'producto' => $producto,
                        'conteo' => $conteo
                    ];

                }else{
                    return [
                        'success' => 2  // no tiene carrito de compras
                    ];
                }
            }catch(\Error $e){
                return [
                    'success' => 3, // error
                ];
            }
        }
        else{
            return ['success' => 4]; // usuario no encontrado
        }
    }




    public function borrarProductoDelCarritoModoTesteo(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar si tenemos carrito
        if(ProductoTesteoCarrito::where('id_cliente', $request->clienteid)->first()){

            // encontrar el producto a borrar
            if(ProductoTesteoCarrito::where('id', $request->carritoid)->first()){
                ProductoTesteoCarrito::where('id', $request->carritoid)->delete();

                return ['success' => 1]; // producto eliminado
            }else{
                // producto a borrar no encontrado
                return ['success' => 1];
            }
        }else{
            // carrito de compras borrado
            return ['success' => 1 ];
        }
    }






    public function verOrdenAProcesarClienteModoTesteo(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }


        try {

            // direccion del cliente
            $infoDireccion = DireccionCliente::where('id_cliente', $request->clienteid)
                ->where('seleccionado', 1)
                ->first();


            // listado de productos del carrito de modo testeo
            $producto = ProductoTesteoCarrito::where('id_cliente', $request->clienteid)->get();

            $subtotal = 0;
            // multiplicar precio x cantidad
            foreach($producto as $p){

                $infoProducto = Productos::where('id', $p->id_producto)->first();

                $multi = $p->cantidad * $infoProducto->precio;
                $subtotal = $subtotal + $multi;
            }

            $total = '$' . number_format((float)$subtotal, 2, '.', ',');


            return [
                'success' => 1,
                'total' => $total,
                'direccion' => $infoDireccion->direccion,
                'cliente' => $infoDireccion->nombre,
            ];


        }catch(\Error $e){
            Log::info('erorr ' . $e);
            return ['success' => 99];
        }
    }



    public function finalizarModoTesteo(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        $fecha = Carbon::now('America/El_Salvador');

        // eliminar lista de productos del modo testeo
        ProductoTesteoCarrito::where('id_cliente', $request->clienteid)->delete();

        // guardar que ya realizo la prueba

        $extra = new ClienteModoTesteo();
        $extra->id_cliente = $request->clienteid;
        $extra->fecha = $fecha;
        $extra->save();


        return ['success' => 1];

    }










}
