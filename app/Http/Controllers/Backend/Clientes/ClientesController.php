<?php

namespace App\Http\Controllers\Backend\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Clientes;
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

        $lista = Clientes::orderBy('usuario')->get();

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

        return "vista";
    }





}
