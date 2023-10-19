<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\HorarioServicio;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesMotoristas;
use App\Models\Servicios;
use App\Models\UsuariosServicios;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiciosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index(){

        // no quiero las zonas que ya han sido utilizadas
        $listaServicios = Servicios::all();
        $pilaIdZonas = array();

        foreach ($listaServicios as $info){
            array_push($pilaIdZonas, $info->id_zonas);
        }

        return view('backend.admin.configuracion.servicios.vistaservicios', );
    }

    // tabla
    public function serviciosTabla(){

        $servicios = Servicios::orderBy('nombre')->get();

        foreach ($servicios as $info){
            $info->minimo = '$' . number_format((float)$info->minimo, 2, '.', ',');
        }

        return view('backend.admin.configuracion.servicios.tablaservicios', compact('servicios'));
    }


    public function registrarServicio(Request $request)
    {

        DB::beginTransaction();

        try {

            $tipo = new Servicios();

            $tipo->nombre = $request->nombre;
            $tipo->utiliza_cupon = $request->togglecupon;
            $tipo->save();

            // guardar Horarios

            $hora1 = new HorarioServicio();
            $hora1->id_servicios = $tipo->id;
            $hora1->hora1 = $request->horalunes1;
            $hora1->hora2 = $request->horalunes2;
            $hora1->dia = 2;
            $hora1->cerrado = $request->cbcerradolunes;
            $hora1->save();

            $hora2 = new HorarioServicio();
            $hora2->id_servicios = $tipo->id;
            $hora2->hora1 = $request->horamartes1;
            $hora2->hora2 = $request->horamartes2;
            $hora2->dia = 3;
            $hora2->cerrado = $request->cbcerradomartes;
            $hora2->save();

            $hora3 = new HorarioServicio();
            $hora3->id_servicios = $tipo->id;
            $hora3->hora1 = $request->horamiercoles1;
            $hora3->hora2 = $request->horamiercoles2;
            $hora3->dia = 4;
            $hora3->cerrado = $request->cbcerradomiercoles;
            $hora3->save();

            $hora4 = new HorarioServicio();
            $hora4->id_servicios = $tipo->id;
            $hora4->hora1 = $request->horajueves1;
            $hora4->hora2 = $request->horajueves2;
            $hora4->dia = 5;
            $hora4->cerrado = $request->cbcerradojueves;
            $hora4->save();

            $hora5 = new HorarioServicio();
            $hora5->id_servicios = $tipo->id;
            $hora5->hora1 = $request->horaviernes1;
            $hora5->hora2 = $request->horaviernes2;
            $hora5->dia = 6;
            $hora5->cerrado = $request->cbcerradoviernes;
            $hora5->save();

            $hora6 = new HorarioServicio();
            $hora6->id_servicios = $tipo->id;
            $hora6->hora1 = $request->horasabado1;
            $hora6->hora2 = $request->horasabado2;
            $hora6->dia = 7;
            $hora6->cerrado = $request->cbcerradosabado;
            $hora6->save();

            $hora7 = new HorarioServicio();
            $hora7->id_servicios = $tipo->id;
            $hora7->hora1 = $request->horadomingo1;
            $hora7->hora2 = $request->horadomingo2;
            $hora7->dia = 1;
            $hora7->cerrado = $request->cbcerradodomingo;
            $hora7->save();

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {

            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function informacionServicio(Request $request){

        if($servicio = Servicios::where('id', $request->id)->first()){

            return['success' => 1, 'servicio' => $servicio];
        }else{
            return['success' => 2];
        }
    }


    public function editarServicios(Request $request){

        if(Servicios::where('id', $request->id)->first()) {


            Servicios::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'utiliza_cupon' => $request->togglecupon,
            ]);

            DB::commit();
            return ['success' => 1];

        }else{
            return ['success' => 99];
        }
    }


    public function informacionHorarios(Request $request){

        if(HorarioServicio::where('id', $request->id)->first()){

            $horario = HorarioServicio::where('id_servicios', $request->id)->get();

            return['success' => 1, 'horario' => $horario];
        }else{
            return['success' => 2];
        }
    }


    public function editarHorarioServicio(Request $request){

        DB::beginTransaction();

        try {

            HorarioServicio::where('id_servicios', $request->id)->where('dia', 1)->update(['hora1' => $request->horadomingo1, 'hora2' => $request->horadomingo2, 'cerrado' => $request->cbcerradodomingo]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 2)->update(['hora1' => $request->horalunes1, 'hora2' => $request->horalunes2, 'cerrado' => $request->cbcerradolunes]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 3)->update(['hora1' => $request->horamartes1, 'hora2' => $request->horamartes2, 'cerrado' => $request->cbcerradomartes]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 4)->update(['hora1' => $request->horamiercoles1, 'hora2' => $request->horamiercoles2, 'cerrado' => $request->cbcerradomiercoles]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 5)->update(['hora1' => $request->horajueves1, 'hora2' => $request->horajueves2, 'cerrado' => $request->cbcerradojueves]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 6)->update(['hora1' => $request->horaviernes1, 'hora2' => $request->horaviernes2, 'cerrado' => $request->cbcerradoviernes]);
            HorarioServicio::where('id_servicios', $request->id)->where('dia', 7)->update(['hora1' => $request->horasabado1, 'hora2' => $request->horasabado2, 'cerrado' => $request->cbcerradosabado]);

            DB::commit();

            return ['success' => 1];

        } catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }




    //**********************************************************


    public function indexUsuariosRestaurantes(){

        $servicios = Servicios::orderBy('nombre')->get();
        return view('backend.admin.usuarios.vistausuarios', compact('servicios'));
    }


    public function tablaUsuariosRestaurantes(){

        $lista = UsuariosServicios::orderBy('id', 'ASC')
            ->where('bloqueado', 0)
            ->get();

        foreach ($lista as $info){

            $infoServicio = Servicios::where('id', $info->id_servicios)->first();

            $info->restaurante = $infoServicio->nombre;

            if($info->fecha != null){
                $info->fecha = date("h:i A d-m-Y", strtotime($info->fecha_entroapp));
            }

        }

        return view('backend.admin.usuarios.tablausuarios', compact('lista'));
    }


    public function registrarUsuarioRestaurante(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'usuario' => 'required',
            'password' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // SOLO 1 USUARIO POR RESTAURANTE Y SE IGNORAN LOS BLOQUEADOS
        if(UsuariosServicios::where('id_servicios', $request->idservicio)
            ->where('bloqueado', 0)
            ->first()){

            return ['success' => 1];
        }

        // NO USUARIOS REPETIDOS
        if(UsuariosServicios::where('usuario', $request->usuario)
            ->first()){
            return ['success' => 2];
        }

        // obtener los usuarios que han sido bloqueados

        $usuario = new UsuariosServicios();
        $usuario->id_servicios = $request->idservicio;
        $usuario->usuario = $request->usuario;
        $usuario->password = Hash::make($request->password);
        $usuario->token_fcm = null;
        $usuario->bloqueado = 0;
        $usuario->nombre = $request->nombre;

        if($usuario->save()){
            return ['success' => 3];
        }else{
            return ['success' => 99];
        }
    }



    public function bloquearUsuarioRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        UsuariosServicios::where('id', $request->id)->update([
            'bloqueado' => 1
        ]);

        return ['success' => 1];
    }


    public function informacionUsuarioRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = UsuariosServicios::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }

    }


    public function actualizarUsuarioRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'usuario' => 'required',
        );

        // password

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // VERIFICAR USUARIO NO REPETIDO
        if(UsuariosServicios::where('usuario', $request->usuario)
            ->where('id', '!=', $request->id)->first()){

            return ['success' => 1];
        }

        // ACTUALIZAR DATOS



        UsuariosServicios::where('id', $request->id)->update([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
        ]);

        if($request->password != null){
            UsuariosServicios::where('id', $request->id)->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return ['success' => 2];
    }



    ///*******************************************************************


    public function indexMotoristasRestaurantes(){
        $servicios = Servicios::orderBy('nombre')->get();

        return view('backend.admin.motoristas.vistamotoristas', compact('servicios'));
    }


    public function tablaMotoristasRestaurantes(){

        $lista = MotoristasServicios::orderBy('usuario', 'ASC')->get();

        foreach ($lista as $info){

            $infoServicio = Servicios::where('id', $info->id_servicios)->first();
            $info->restaurante = $infoServicio->nombre;
        }

        return view('backend.admin.motoristas.tablamotoristas', compact('lista'));
    }


    public function registrarMotoristaRestaurante(Request $request){

        $regla = array(
            'idservicio' => 'required',
            'usuario' => 'required',
            'password' => 'required',
            'nombre' => 'required',
            'vehiculo' => 'required',
            'placa' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // NO USUARIOS REPETIDOS
        if(MotoristasServicios::where('usuario', $request->usuario)
            ->first()){
            return ['success' => 1];
        }

        // obtener los usuarios que han sido bloqueados


        $cadena = Str::random(15);
        $tiempo = microtime();
        $union = $cadena.$tiempo;
        $nombre = str_replace(' ', '_', $union);

        $extension = '.'.$request->imagen->getClientOriginalExtension();
        $nombreFoto = $nombre.strtolower($extension);
        $avatar = $request->file('imagen');
        $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

        if($upload){

            $usuario = new MotoristasServicios();
            $usuario->id_servicios = $request->idservicio;
            $usuario->usuario = $request->usuario;
            $usuario->password = Hash::make($request->password);
            $usuario->token_fcm = null;
            $usuario->activo = 1;
            $usuario->nombre = $request->nombre;
            $usuario->vehiculo = $request->vehiculo;
            $usuario->placa = $request->placa;
            $usuario->imagen = $nombreFoto;
            $usuario->notificacion = 1;


            if($usuario->save()){
                return ['success' => 2];
            }else{
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }


    public function informacionMotoristaRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($info = MotoristasServicios::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }

    }


    public function actualizarMotoristaRestaurante(Request $request){


        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'usuario' => 'required',
            'activo' => 'required',
            'vehiculo' => 'required',
            'placa' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // VERIICAR USUARIO NO REPETIDO
        if(MotoristasServicios::where('usuario', $request->usuario)
            ->where('id', '!=', $request->id)->first()){

            return ['success' => 1];
        }

        // ACTUALIZAR DATOS


        $infoMotorista = MotoristasServicios::where('id', $request->id)->first();


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
                $imagenOld = $infoMotorista->imagen;


                MotoristasServicios::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'usuario' => $request->usuario,
                    'activo' => $request->activo,
                    'vehiculo' => $request->vehiculo,
                    'placa' => $request->placa,
                    'imagen' => $nombreFoto
                ]);

                if($request->password != null){
                    MotoristasServicios::where('id', $request->id)->update([
                        'password' => Hash::make($request->password)
                    ]);
                }

                if(Storage::disk('imagenes')->exists($imagenOld)){
                    Storage::disk('imagenes')->delete($imagenOld);
                }

                return ['success' => 2];

            }else{
                return ['success' => 99];
            }
        }else{
            // solo guardar datos

            MotoristasServicios::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'usuario' => $request->usuario,
                'activo' => $request->activo,
                'vehiculo' => $request->vehiculo,
                'placa' => $request->placa,
            ]);

            if($request->password != null){
                MotoristasServicios::where('id', $request->id)->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return ['success' => 2];
        }
    }




    public function indexMotoOrdenPendiente(){

        return view('backend.admin.motoristas.pendientecompletar.vistapendienteorden');
    }



    public function tablaMotoOrdenPendiente(){

        $listado = Ordenes::where('estado_preparada', 1)
            ->where('estado_entregada', 0)
            ->where('estado_cancelada', 0)
            ->orderBy('fecha_orden')
            ->get();

        foreach ($listado as $dato){

            $dato->fecha_orden = date("d-m-Y h:i A", strtotime($dato->fecha_orden));

            $infoServicio = Servicios::where('id', $dato->id_servicio)->first();
            $dato->nombreservicio = $infoServicio->nombre;

            $nombremoto = "Pendiente Seleccionar";

            if($infoMotoOrden = OrdenesMotoristas::where('id_ordenes', $dato->id)->first()){

                $infoMoto = MotoristasServicios::where('id', $infoMotoOrden->id_motorista)->first();

                $nombremoto = $infoMoto->nombre;
            }

            $dato->nombremoto = $nombremoto;
        }


        return view('backend.admin.motoristas.pendientecompletar.tablapendienteorden', compact('listado'));
    }




}
