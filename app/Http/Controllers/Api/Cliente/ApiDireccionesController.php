<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Zonas;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiDireccionesController extends Controller
{

    public function listadoDeDirecciones(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            $conteo = DireccionCliente::where('id_cliente', $request->id)->count();

            if($conteo == 0){
                // cliente no tiene direccion

                $titulo = "Nota";
                $mensaje = "Registrar una nueva Dirección";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];

            }else{
                $direccion = DireccionCliente::where('id_cliente', $request->id)->get();

                return ['success' => 2, 'direcciones' => $direccion];
            }

        }else{
            return ['succcess'=> 99];
        }
    }


    // retorna listado de zonas
    public function puntosZonaPoligonos(){

        $zonas = Zonas::where('activo', 1)->get();

        $resultsBloque = array();
        $index = 0;

        foreach($zonas  as $secciones){
            array_push($resultsBloque,$secciones);

            $subSecciones = DB::table('zonas_poligono AS pol')
                ->select('pol.latitud AS latitudPoligono', 'pol.longitud AS longitudPoligono')
                ->where('pol.id_zonas', $secciones->id)
                ->get();

            $resultsBloque[$index]->poligonos = $subSecciones;
            $index++;
        }

        return [
            'success' => 1,
            'poligono' => $zonas
        ];
    }


    // registrar direccion de cliente
    public function nuevaDireccionCliente(Request $request){
        $reglaDatos = array(
            'id' => 'required',
            'nombre' => 'required',
            'direccion' => 'required',
            'id_zona' => 'required',
            'telefono' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                $di = new DireccionCliente();
                $di->id_zonas = $request->id_zona;
                $di->id_cliente = $request->id;
                $di->nombre = $request->nombre;
                $di->telefono = $request->telefono;
                $di->direccion = $request->direccion;
                $di->punto_referencia = $request->punto_referencia;
                $di->seleccionado = 1;
                $di->latitud = $request->latitud;
                $di->longitud = $request->longitud;
                $di->latitudreal = $request->latitudreal;
                $di->longitudreal = $request->longitudreal;

                if($di->save()){

                    try {
                        DireccionCliente::where('id_cliente', $request->id)
                            ->where('id', '!=', $di->id)
                            ->update(['seleccionado' => 0]);


                        $infoZonaServicio = ZonasServicio::where('id_zonas', $request->id_zona)->first();


                        // COMO SELECCIONA EN EL MAPA, PREGUNTAR SI TIENE CARRITO EL CLIENTE
                        // SI EL SERVICIO ES EL MISMO, NO BORRAR CARRITO, PERO SI ES OTRA ZONA CON OTRO RESTAURANTE
                        // AHI SI BORRAR CARRITO DE COMPRAS

                        if($infoCarritoTempo = CarritoTemporal::where('id_clientes', $request->id)->first()){

                            if($infoZonaServicio->id_servicios == $infoCarritoTempo->id_servicios){
                                // NO BORRAR CARRITO YA QUE ES DEL MISMO SERVICIO
                            }else{

                                // SI BORRARLE CARRITO

                                CarritoExtra::where('id_carrito_temporal', $infoCarritoTempo->id)->delete();
                                CarritoTemporal::where('id_clientes', $request->id)->delete();
                            }
                        }






                        DB::commit();

                        return ['success' => 1];

                    }  catch (\Exception $ex) {
                        DB::rollback();

                        return ['success' => 99]; // error
                    }
                }else{
                    return ['success' => 99]; // error
                }

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }




}
