<?php

namespace App\Http\Controllers\Backend\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\HorarioServicio;
use App\Models\Servicios;
use App\Models\Zonas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $tipo->tiempo_cocina = 10; // tiempo que da cocina para preparar orden por defecto
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

            DB::beginTransaction();
            try {

                Servicios::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'utiliza_cupon' => $request->togglecupon,
                ]);

                DB::commit();

                return ['success' => 1];

            } catch (\Throwable $e) {

                DB::rollback();
                return ['success' => 99];
            }
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



}
