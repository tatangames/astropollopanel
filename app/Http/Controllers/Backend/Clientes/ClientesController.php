<?php

namespace App\Http\Controllers\Backend\Clientes;

use App\Http\Controllers\Controller;
use App\Models\ClienteModoTesteo;
use App\Models\Clientes;
use App\Models\DireccionCliente;
use App\Models\Servicios;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index(){

        return view('backend.admin.clientes.lista.vistaclientes');
    }


    public function tablaClientes(){

        // NO MOSTRAR 3 CLIENTE
        // QUE SON PARA CALL CENTER Y ACCESO DE APP

        $lista = Clientes::orderBy('usuario')
            ->whereNotIn('id', [1,2,3])
            ->get();

        foreach ($lista as $info){

            $info->fecha = date("d-m-Y h:i A", strtotime($info->fecha));
        }

        return view('backend.admin.clientes.lista.tablavistaclientes', compact('lista'));
    }


    public function informacionCliente(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = Clientes::where('id', $request->id)->first()){

            return ['success' => 1, 'cliente' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarCliente(Request $request){

        $rules = array(
            'id' => 'required',
            'activo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            Clientes::where('id', $request->id)->update([
                'activo' => $request->activo
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }



    //*******************   DIRECCIONES DEL CLIENTE  /********

    public function indexListaDirecciones($id){

        return view('backend.admin.clientes.direcciones.vistadireccion', compact('id'));
    }


    public function tablaClientesDirecciones($id){

        $lista = DireccionCliente::where('id_cliente', $id)->get();

        return view('backend.admin.clientes.direcciones.tabladireccion', compact('lista'));
    }

    public function infoCoordenadasReales(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = DireccionCliente::where('id', $request->id)->first()){

            if(empty($info->latitudreal)){
                return ['success' => 1];
            }

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }



    public function mapaDireccionRegistrado($id){

        $googleapi = config('googleapi.Google_API');

        $poligono = DireccionCliente::where('id', $id)->first();

        $latitud = $poligono->latitud;
        $longitud = $poligono->longitud;

        return view('backend.admin.clientes.direcciones.mapa.maparegistrado', compact('latitud', 'longitud', 'googleapi'));
    }


    public function mapaDireccionReal($id){
        $googleapi = config('googleapi.Google_API');

        $poligono = DireccionCliente::where('id', $id)->first();

        $latitud = $poligono->latitudreal;
        $longitud = $poligono->longitudreal;

        return view('backend.admin.clientes.direcciones.mapa.mapareal', compact('latitud', 'longitud', 'googleapi'));
    }




    public function indexClientesModoPrueba(){

        return view('backend.admin.clientes.modoprueba.vistaclientesmodoprueba');
    }


    public function tablaClientesModoPrueba(){

        $listado = ClienteModoTesteo::orderBy('fecha')->get();

        foreach ($listado as $dato){

            $infoCliente = Clientes::where('id', $dato->id_cliente)->first();

            $dato->usuario = $infoCliente->usuario;

            $dato->fecha = date("d-m-Y h:i A", strtotime($dato->fecha));
        }

        return view('backend.admin.clientes.modoprueba.tablaclientesmodoprueba', compact('listado'));
    }



    public function borrarClienteModoTesteo(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        ClienteModoTesteo::where('id', $request->id)->delete();


        return ['success' => 1];
    }


    public function indexClientePrimeraUbicacion(){


        // obtener lista de restaurantes
        $listaRestaurantes = Servicios::orderBy('nombre')->get();


        foreach ($listaRestaurantes as $info){

            $pilaId = array();

            $infoZonaServicios = ZonasServicio::where('id_servicios', $info->id)->get();

            foreach ($infoZonaServicios as $datoZona){
                array_push($pilaId, $datoZona->id_zonas);
            }

            // listado de cliente direccion que pertenecen a estas zonas
            $conteo = DireccionCliente::whereIn('id_zonas', $pilaId)->count();

            $info->conteo = $conteo;
        }



        return view('backend.admin.reportes.clientes.vistaclientesrestaurantes', compact('listaRestaurantes'));
    }



}
