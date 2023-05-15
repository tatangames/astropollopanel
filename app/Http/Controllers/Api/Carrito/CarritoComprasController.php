<?php

namespace App\Http\Controllers\Api\Carrito;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Categorias;
use App\Models\DireccionCliente;
use App\Models\Productos;
use App\Models\SubCategorias;
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
                // guardar producto
                $carrito = new CarritoTemporal();
                $carrito->id_clientes = $request->clienteid;
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




}
