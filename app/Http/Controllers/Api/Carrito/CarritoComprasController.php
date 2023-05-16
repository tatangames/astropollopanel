<?php

namespace App\Http\Controllers\Api\Carrito;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Categorias;
use App\Models\Clientes;
use App\Models\CuponDescuentoDinero;
use App\Models\CuponDescuentoPorcentaje;
use App\Models\Cupones;
use App\Models\CuponProductoGratis;
use App\Models\DireccionCliente;
use App\Models\Productos;
use App\Models\Servicios;
use App\Models\SubCategorias;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CarritoComprasController extends Controller
{

    public function agregarProductoCarritoTemporal(Request $request){

        $reglaDatos = array(
            'productoid' => 'required',
            'clienteid' => 'required',
            'cantidad' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }


        // REGLA 1: cliente no tiene direccion
        if(!DireccionCliente::where('id_cliente', $request->clienteid)->first()){
            $titulo = "Nota";
            $mensaje = "No se encontro una dirección de envío";
            return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }

        DB::beginTransaction();

        try {

            //**** VALIDACIONES

            // NO SE VALIDARA EL HORARIO DEL SERVICIO
            // PERO SE TOMARA EL HORARIO DE LA CATEGORIA QUE ESTE
            // (Activo y horario)

            $getValores = Carbon::now('America/El_Salvador');
            $hora = $getValores->format('H:i:s');

            $infoProducto = Productos::where('id', $request->productoid)->first();
            $infoSubCategoria = SubCategorias::where('id', $infoProducto->id_subcategorias)->first();
            $infoCategoria = Categorias::where('id', $infoSubCategoria->id_categorias)->first();

            // REGLA 2: producto debe estar activo
            if($infoProducto->activo == 0){
                $titulo = "Nota";
                $mensaje = "Producto no esta disponible por el momento";
                return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // REGLA 3: sub categoria del producto debe estar activo
            if($infoSubCategoria->activo == 0){
                $titulo = "Nota";
                $mensaje = "Producto no esta disponible por el momento";

                return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // REGLA 4: la categoria del producto debe estar activo
            if($infoCategoria->activo == 0){
                $titulo = "Nota";
                $mensaje = "Producto no esta disponible por el momento";
                return ['success' => 4, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            if($infoCategoria->usa_horario == 1){

                // REGLA 5: la categoria si utiliza horario debe estar disponible
                $horaCategoria = Categorias::where('id', $infoSubCategoria->id_categorias)
                    ->where('usa_horario', 1)
                    ->where('hora_abre', '<=', $hora)
                    ->where('hora_cierra', '>=', $hora)
                    ->get();

                if(count($horaCategoria) >= 1){
                    // ABIERTO
                }else{
                    $mensaje = "Producto no esta disponible por el momento -";
                    $titulo = "Nota";
                    return ['success' => 5, 'titulo' => $titulo, 'mensaje' => $mensaje];
                }
            }


            // verificar si cliente tiene carrito de compras sino solo agregar

            if($infoC = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){
                $extra = new CarritoExtra();
                $extra->id_carrito_temporal = $infoC->id;
                $extra->id_producto = $request->productoid;
                $extra->cantidad = $request->cantidad;
                $extra->nota_producto = $request->notaproducto;
                $extra->save();
            }else{
                // guardar carrito temporal
                $carrito = new CarritoTemporal();
                $carrito->id_clientes = $request->clienteid;
                $carrito->id_servicios = $infoCategoria->id_servicios;
                $carrito->save();

                // guardar producto
                $idcarrito = $carrito->id;
                $extra = new CarritoExtra();
                $extra->id_carrito_temporal = $idcarrito;
                $extra->id_producto = $request->productoid;
                $extra->cantidad = $request->cantidad;
                $extra->nota_producto = $request->notaproducto;
                $extra->save();
            }

            DB::commit();

            // producto guardado
            return ['success' => 6];

        }catch(\Error $e){
            Log::info('error' . $e);
            DB::rollback();

            return [
                'success' => 100
            ];
        }
    }



    // ver carrito de compras
    public function verCarritoDecompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );


        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->clienteid)->first()){

            try {

                // PARA NO PASAR A PROCESAR ORDEN
                $estadoProductoActivo = 1;


                $getValores = Carbon::now('America/El_Salvador');
                $hora = $getValores->format('H:i:s');



                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){
                    $producto = DB::table('productos AS p')
                        ->join('carrito_extra AS c', 'c.id_producto', '=', 'p.id')
                        ->select('p.id AS productoID', 'p.nombre', 'c.cantidad',
                            'p.imagen', 'p.precio', 'p.activo','c.id AS carritoid', 'p.utiliza_imagen', 'p.id_subcategorias')
                        ->where('c.id_carrito_temporal', $cart->id)
                        ->get();

                    // verificar cada producto
                    foreach ($producto as $pro) {

                        $estadoLocal = 0;
                        $titulo = "";
                        $mensaje = "";

                        // PRODUCTO NO ACTIVO
                        if($pro->activo == 0){
                            $estadoLocal = 1;
                            $estadoProductoActivo = 0;
                            $titulo = "Producto no disponible";
                            $mensaje = "Por favor eliminarlo de su carrito de compras deslizando hacia los lados. Gracias.";
                        }

                        // CONOCER SI LA SUB CATEGORIA ESTA ACTIVA
                        $infoSubCate = SubCategorias::where('id', $pro->id_subcategorias)->first();

                        if($infoSubCate->activo == 0){
                            $estadoLocal = 1;
                            $estadoProductoActivo = 0;
                            $titulo = "Producto no disponible";
                            $mensaje = "Por favor eliminarlo de su carrito de compras deslizando hacia los lados. Gracias.";
                        }

                        // CONOCER SI LA CATEGORIA DEL PRODUCTO ESTA ACTIVA
                        $infoCategoria = Categorias::where('id', $infoSubCate->id_categorias)->first();

                        if($infoCategoria->activo == 0){
                            $estadoLocal = 1;
                            $estadoProductoActivo = 0;
                            $titulo = "Producto no disponible";
                            $mensaje = "Por favor eliminarlo de su carrito de compras deslizando hacia los lados. Gracias.";
                        }


                        if($infoCategoria->usa_horario == 1){
                            // CONOCER SI LA CATEGORIA TIENE HORARIO Y VER SI ESTA DISPONIBLE
                            $horaCategoria = Categorias::where('id', $infoSubCate->id_categorias)
                                ->where('activo', 1)
                                ->where('usa_horario', 1)
                                ->where('hora_abre', '<=', $hora)
                                ->where('hora_cierra', '>=', $hora)
                                ->get();

                            if(count($horaCategoria) >= 1){
                                // ABIERTO
                            }else{

                                // enviar formateados horario por si se utilizando
                                $hora_abre = date("h:i A", strtotime($infoCategoria->hora_abre));
                                $hora_cierra = date("h:i A", strtotime($infoCategoria->hora_cierra));

                                $estadoLocal = 1;
                                $estadoProductoActivo = 0;
                                $titulo = "Producto no disponible";
                                $mensaje = "Este Producto esta disponible de " . $hora_abre . " A " . $hora_cierra;
                            }
                        }


                        // multiplicar cantidad por el precio de cada producto
                        $precio = $pro->cantidad * $pro->precio;


                        // estado de productos segun reglas
                        $pro->estadoLocal = $estadoLocal;


                        $pro->titulo = $titulo;
                        $pro->mensaje = $mensaje;

                        // convertir
                        $pro->precioformat = '$' . number_format((float)$precio, 2, '.', ',');
                    }

                    // sub total de la orden
                    $subTotal = collect($producto)->sum('precio'); // sumar todos el precio

                    return [
                        'success' => 1,
                        'subtotal' => number_format((float)$subTotal, 2, '.', ','), // subtotal
                        'estadoProductoGlobal' => $estadoProductoActivo,
                        'producto' => $producto,
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




    public function borrarCarritoDeCompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if($carrito = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){
            CarritoExtra::where('id_carrito_temporal', $carrito->id)->delete();
            CarritoTemporal::where('id_clientes', $request->clienteid)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    public function borrarProductoDelCarrito(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar si tenemos carrito
        if($ctm = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){

            // encontrar el producto a borrar
            if(CarritoExtra::where('id', $request->carritoid)->first()){
                CarritoExtra::where('id', $request->carritoid)->delete();

                // saver si tenemos mas productos aun
                $dato = CarritoExtra::where('id_carrito_temporal', $ctm->id)->get();

                if(count($dato) == 0){
                    CarritoTemporal::where('id', $ctm->id)->delete();
                    return ['success' => 1]; // carrito de compras borrado
                }

                return ['success' => 2]; // producto eliminado
            }else{
                // producto a borrar no encontrado
                return ['success' => 3];
            }
        }else{
            // carrito de compras borrado
            return ['success' => 1 ];
        }
    }



    public function verProductoCarritoEditar(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required' //es id del carrito extra
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(CarritoTemporal::where('id_clientes', $request->clienteid)->first()){

            if(CarritoExtra::where('id', $request->carritoid)->first()){

                // informacion del producto + cantidad elegida
                $producto = DB::table('productos AS p')
                    ->join('carrito_extra AS c', 'c.id_producto', '=', 'p.id')
                    ->select('p.id AS productoID', 'p.nombre', 'p.descripcion', 'c.cantidad', 'c.nota_producto',
                        'p.imagen', 'p.precio', 'p.utiliza_nota', 'p.nota', 'p.utiliza_imagen')
                    ->where('c.id', $request->carritoid)
                    ->first();

                return [
                    'success' => 1,
                    'producto' => $producto,
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            // no tiene carrito
            return ['success' => 3];
        }
    }


    public function editarCantidadProducto(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'cantidad' => 'required',
            'carritoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar carrito de compras a quien pertenece el producto
        // verificar si existe el carrito
        if(CarritoTemporal::where('id_clientes', $request->clienteid)->first()){
            // verificar si existe el carrito extra id que manda el usuario
            if(CarritoExtra::where('id', $request->carritoid)->first()){

                CarritoExtra::where('id', $request->carritoid)->update(['cantidad' => $request->cantidad,
                    'nota_producto' => $request->nota]);

                return [
                    'success' => 1 // cantidad actualizada
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }




    public function verOrdenAProcesarCliente(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar que cliente tenga direccion
        if(!DireccionCliente::where('id_cliente', $request->clienteid)->first()){
            // sin direccion
            $titulo = "Nota";
            $mensaje = "No se encontro una dirección de envío";
            return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }

        try {
            // preguntar si usuario ya tiene un carrito de compras
            if($cart = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){

                $infoDireccion = DireccionCliente::where('id_cliente', $request->clienteid)
                    ->where('seleccionado', 1)
                    ->first();

                // listado de productos del carrito
                $producto = DB::table('productos AS p')
                    ->join('carrito_extra AS c', 'c.id_producto', '=', 'p.id')
                    ->select('p.precio', 'c.cantidad')
                    ->where('c.id_carrito_temporal', $cart->id)
                    ->get();

                $subtotal = 0;
                // multiplicar precio x cantidad
                foreach($producto as $p){

                    $cantidad = $p->cantidad;
                    $precio = $p->precio;
                    $multi = $cantidad * $precio;
                    $subtotal = $subtotal + $multi;
                }

                // VERIFICAR SI ZONA UTILIZA MINIMO
                $infoZona = Zonas::where('id', $infoDireccion->id_zonas)->first();
                $boolMinimo = 0; // aqui no puede ordenar

                $msjMinimoConsumo = "El mínimo de consumo es: $".$infoZona->minimo_consumo;


                if($subtotal >= $infoZona->minimo_consumo){
                    // si puede ordenar
                    $boolMinimo = 1;
                }

                $total = '$' . number_format((float)$subtotal, 2, '.', '');


                $infoServicios = Servicios::where('id', $cart->id_servicios)->first();


                return [
                    'success' => 2,
                    'total' => $total,
                    'direccion' => $infoDireccion->direccion,
                    'cliente' => $infoDireccion->nombre,
                    'minimo' => $boolMinimo,
                    'mensaje' => $msjMinimoConsumo,
                    'usacupon' => $infoServicios->utiliza_cupon,
                ];

            }else{
                // no tiene carrito de compras
                return ['success' => 3];
            }
        }catch(\Error $e){
            return ['success' => 4, 'err' => $e];
        }
    }





    public function verificarCupon(Request $request){



            // RETORNOS

            // 1: carrito de compras no encontrado
            // 1: cupon no valido
            // 2: cupon producto gratis
            // 3: cupon descuento de dinero
            // 4: cupon descuento de porcentaje


            // verificar si usuario tiene carrito de compras
            if($cart = CarritoTemporal::where('id_clientes', $request->clienteid)->first()){

                // EL USUARIO SIEMPRE TENDRA UNA DIRECCION AQUI

                // verificar que tipo de cupon es y si aun es valido
                if($infoCupon = Cupones::where('texto_cupon', $request->cupon)->first()){

                    // CUPON ACTIVO
                    if($infoCupon->activo == 0){
                        $titulo = "Nota";
                        $mensaje = "Cupón no válido";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                    }


                    // CUPON LIMITE ALCANZADO
                    if($infoCupon->contador >= $infoCupon->uso_limite){
                        $titulo = "Nota";
                        $mensaje = "Cupón no válido";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                    }


                    // listado de productos del carrito
                    $producto = DB::table('productos AS p')
                        ->join('carrito_extra AS c', 'c.id_producto', '=', 'p.id')
                        ->select('p.precio', 'c.cantidad')
                        ->where('c.id_carrito_temporal', $cart->id)
                        ->get();

                    $subtotal = 0;
                    // multiplicar precio x cantidad
                    foreach($producto as $p){

                        $cantidad = $p->cantidad;
                        $precio = $p->precio;
                        $multi = $cantidad * $precio;
                        $subtotal = $subtotal + $multi;
                    }

                    // CONOCER QUE SERVICIO USA EL CUPON

                    // * PRODUCTO GRATIS

                    if($infoCupon->id_tipo_cupon == 1){

                        // VERIFICAR QUE EL SERVICIO DONDE ESTOY COMPRANDO ACEPTA ESTE CUPON
                        if($infoCuponProGratis = CuponProductoGratis::where('id_cupones', $infoCupon->id)
                            ->where('id_servicios', $cart->id_servicios)
                            ->first()){

                            // * cupon valido para producto gratis, solo se retorna texto
                            $titulo = "Nota";
                            $mensaje = "Cupón aplica para Producto Gratis";
                            return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje, 'nombre' => $infoCuponProGratis->nombre];

                        }else{
                            $titulo = "Nota";
                            $mensaje = "Cupón no válido";
                            return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                        }
                    }

                    // DESCUENTO DE DINERO
                    else if($infoCupon->id_tipo_cupon == 2) {

                        // VERIFICAR QUE EL SERVICIO DONDE ESTOY COMPRANDO ACEPTA ESTE CUPON
                        if($infoCuponDescuentoDin = CuponDescuentoDinero::where('id_cupones', $infoCupon->id)
                            ->where('id_servicios', $cart->id_servicios)
                            ->first()){

                            $resta = $subtotal - $infoCuponDescuentoDin->dinero;
                            if($resta <= 0){
                                $resta = 0;
                            }

                            $resta = '$' . number_format((float)$resta, 2, '.', '');

                            $aplico = '$' . number_format((float)$infoCuponDescuentoDin->dinero, 2, '.', '');

                            // * cupon valido para producto gratis, solo se retorna texto
                            $titulo = "Nota";
                            $mensaje = "Cupón aplica descuento de " . $aplico;
                            return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje, 'aplico' => $aplico, 'resta' => $resta];

                        }else{
                            $titulo = "Nota";
                            $mensaje = "Cupón no válido";
                            return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                        }

                    }

                    // DESCUENTO DE PORCENTAJE
                    else if($infoCupon->id_tipo_cupon == 3) {

                        // VERIFICAR QUE EL SERVICIO DONDE ESTOY COMPRANDO ACEPTA ESTE CUPON
                        if($infoCuponDescuentoPor = CuponDescuentoPorcentaje::where('id_cupones', $infoCupon->id)
                            ->where('id_servicios', $cart->id_servicios)
                            ->first()){

                            $aplico = '%' . $infoCuponDescuentoPor->porcentaje;

                            $resta = $subtotal * ($infoCuponDescuentoPor->porcentaje / 100);
                            $final = $subtotal - $resta;

                            if($final <= 0){
                                $final = 0;
                            }

                            $final = '$' . number_format((float)$final, 2, '.', '');


                            // * cupon valido para producto gratis, solo se retorna texto
                            $titulo = "Nota";
                            $mensaje = "Cupón aplica descuento de " . $aplico;
                            return ['success' => 4, 'titulo' => $titulo, 'mensaje' => $mensaje, 'aplico' => $aplico,
                                'resultado' => $final];

                        }else{
                            $titulo = "Nota";
                            $mensaje = "Cupón no válido";
                            return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                        }

                    }

                    // Cupon no encontrado
                    else{

                        $titulo = "Nota";
                        $mensaje = "Cupón no válido";
                        return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];

                    }


                }else{

                    $titulo = "Nota";
                    $mensaje = "Cupón no encontrado";
                    return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
                }
            }else{

                $titulo = "Nota";
                $mensaje = "Cupón no encontrado";
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }
    }


}
