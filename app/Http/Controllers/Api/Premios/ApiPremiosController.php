<?php

namespace App\Http\Controllers\Api\Premios;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
use App\Models\ClientesPremios;
use App\Models\DireccionCliente;
use App\Models\Premios;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiPremiosController extends Controller
{

    public function listaPremiosPorRestaurante(Request $request){

        // RETORNA LISTA DE PREMIOS SOLO ACTIVOS


        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if($infoCliente = Clientes::where('id', $request->clienteid)->first()){

            // OBTENER LA DIRECCION SELECCIONADA PARA OBTENER EL RESTAURANTE
            $infoDireccion = DireccionCliente::where('id_cliente', $infoCliente->id)
                ->where('seleccionado', 1)
                ->first();

            $infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireccion->id_zonas)->first();

            $listaPremios = Premios::where('id_servicio', $infoZonaServicio->id_servicios)
                ->where('activo', 1)
                ->orderBy('puntos', 'ASC')
                ->get();

            foreach ($listaPremios as $info){

                // BUSCAR SI CLIENTE TIENE SELECCIONADO UN PREMIO

                $seleccionado = 0;

                if(ClientesPremios::where('id_clientes', $infoCliente->id)
                    ->where('id_premios', $info->id)->first()){

                    $seleccionado = 1;
                }

                $info->seleccionado = $seleccionado;
            }

            $nota = "Gana Puntos por cada $1.00 al ordenar, para luego canjearlos por diferentes Premios";

            $puntos = "Mis Puntos: " . $infoCliente->puntos;

            return ['success' => 1, 'nota' => $nota, 'puntos' => $puntos, 'listado' => $listaPremios];
        }else{
            return ['success' => 2];
        }
    }


    // EL CLIENTE VA A SELECCIONAR ESTE PREMIO SI LE ALCANZA LOS PUNTOS
    public function seleccionarPremio(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
            'idpremio' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if($infoCliente = Clientes::where('id', $request->clienteid)->first()){

            $infoPremio = Premios::where('id', $request->idpremio)->first();

            // PREMIO NO ACTIVO
            if($infoPremio->activo == 0){
                $titulo = "Nota";
                $mensaje = "El premio ya no esta disponible";
                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            // PUNTOS SI ALCANZA PARA SELECCIONAR
            if($infoCliente->puntos >= $infoPremio->puntos){

                // BORRAR OTRAS SELECCIONES QUE TUVIERA
                ClientesPremios::where('id_clientes', $infoCliente->id)->delete();

                // REGISTRAR NUEVA SELECCION
                $dato = new ClientesPremios();
                $dato->id_clientes = $infoCliente->id;
                $dato->id_premios = $infoPremio->id;

                if($dato->save()){

                    $titulo = "Seleccionado";
                    $mensaje = "Al realizar tu próxima Orden, se canjeara este Premio";

                    return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
                }else{
                    return ['success' => 99];
                }

            }else{
                // PUNTOS INSUFICIENTES
                $titulo = "Nota";
                $mensaje = "Puntos Insuficientes";

                return ['success' => 2, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

        }else{
            return ['success' => 99];
        }
    }


    // DESELECCIONAR EL PREMIO POR EL CLIENTE

    public function deseleccionarPremio(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }

        if($infoCliente = Clientes::where('id', $request->clienteid)->first()){

            ClientesPremios::where('id_clientes', $infoCliente->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }



}
