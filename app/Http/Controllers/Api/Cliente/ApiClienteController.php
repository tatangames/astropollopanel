<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Mail\CorreoPasswordMail;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Clientes;
use App\Models\ClientesPremios;
use App\Models\DireccionCliente;
use App\Models\HorarioServicio;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\OrdenesPremio;
use App\Models\ReporteProblema;
use App\Models\Servicios;
use App\Models\UsuariosServicios;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiClienteController extends Controller
{

    // inicio de sesion para clientes
    public function loginCliente(Request $request){

        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($info = Clientes::where('usuario', $request->usuario)->first()){

            if($info->activo == 0){

                $titulo = 'Nota';
                $mensaje = "Su usuario ha sido Eliminado";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            if (Hash::check($request->password, $info->password)) {

                if($request->idfirebase != null){
                    Clientes::where('id', $info->id)->update(['token_fcm' => $request->idfirebase]);
                }

                // inicio sesion
                return ['success' => 2, 'id' => strval($info->id), 'mensaje' => "Inicio de sesion correctamente"];

            }else{
                // contraseña incorrecta

                $titulo = 'Nota';
                $mensaje = "Su Contraseña es incorrecta";

                return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

        } else {

            $titulo = 'Nota';
            $mensaje = "Usuario no encontrado";

            // usuario no encontrado
            return ['success' => 4, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }
    }

    public function enviarCodigoCorreo(Request $request){

        $rules = array(
            'correo' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if($info = Clientes::where('correo', $request->correo)->first()){

            // codigo aleaotorio
            $codigo = '';
            for($i = 0; $i < 4; $i++) {
                $codigo .= mt_rand(0, 9);
            }

            Clientes::where('id', $info->id)->update(['codigo_correo' => $codigo]);

            $data = ["codigo" => $codigo,
                "usuario" => $info->usuario];

            Mail::to($request->correo)
                ->send(new CorreoPasswordMail($data));


            return ['success' => 1];
        }else{
            // CORREO NO ENCONTRADO
            return ['success' => 2];
        }
    }

    public function verificarCodigoCorreoPassword(Request $request)
    {
        $rules = array(
            'codigo' => 'required',
            'correo' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['success' => 0];
        }

        // verificar codigo
        if ($info = Clientes::where('correo', $request->correo)
            ->where('codigo_correo', $request->codigo)
            ->first()) {


            // puede cambiar contraseña
            return ['success' => 1, 'id' => $info->id];
        } else {

            // codigo incorrecto
            return ['success' => 2];
        }
    }

    // ACTUALIZAR CONTRASENA RECUPERACION POR CORREO
    public function actualizarPasswordClienteCorreo(Request $request){

        $rules = array(
            'id' => 'required',
            'password' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            Clientes::where('id', $request->id)
                ->update(['password' => Hash::make($request->password),
                         'codigo_correo' => null]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    public function informacionCliente(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Clientes::where('id', $request->id)->first()){

            $correo = $info->correo;

            return ['success' => 1, 'usuario' => $info->usuario, 'correo' => $correo];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarCorreoCliente(Request $request){


        $rules = array(
            'id' => 'required',
            'correo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = Clientes::where('id', $request->id)->first()){


            Clientes::where('id', $info->id)->update(['correo' => $request->correo]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionHorarioRestaurante(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            // REGLA: SIEMPRE DEBERA HABER UNA ZONA SERVICIO, SINO ERROR

            // obtener direccion actual del cliente
            $infoDireccion = DireccionCliente::where('id_cliente', $request->id)
                ->where('seleccionado', 1)->first();

            $infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireccion->id_zonas)->first();

            $infoServicio = Servicios::where('id', $infoZonaServicio->id_servicios)->first();

            $infoHorario = HorarioServicio::where('id_servicios', $infoZonaServicio->id_servicios)->get();


            foreach ($infoHorario as $info){
                $hora1 = date("h:i A", strtotime($info->hora1));
                $hora2 = date("h:i A", strtotime($info->hora2));

                if($info->cerrado == 1){
                    $info->horario = "Cerrado";
                    // para ios
                    $info->fechaformat = "Cerrado";

                }else{
                    $info->horario = $hora1 . " / " . $hora2;
                    // para ios
                    $info->fechaformat = $hora1 . " / " . $hora2;
                }

            }

            return ['success' => 1,
                'restaurante' => $infoServicio->nombre,
                'horario' => $infoHorario,];
        }else{
            return ['success' => 2];
        }

    }



    // ACTUALIZAR CONTRASENA DENTRO DEL PERFIL
    public function actualizarPasswordClientePerfil(Request $request){
        $rules = array(
            'id' => 'required',
            'password' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            Clientes::where('id', $request->id)->update(['password' => Hash::make($request->password)]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    public function infoBorrarCarritoComprasCliente(Request $request){


        $rules = array(
            'id' => 'required', // id cliente
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if($info = Clientes::where('id', $request->id)->first()){

            $mensaje = "Al realizar una Orden, puedes elegir si borrar el carrito de compras o mantener los productos para realizar una orden nuevamente";

            return ['success' => 1, 'opcion' => $info->borrar_carrito, 'mensaje' => $mensaje];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarOpcionCarritoCliente(Request $request){


        $rules = array(
            'id' => 'required', // id cliente
            'disponible' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            Clientes::where('id', $request->id)->update([
                'borrar_carrito' => $request->disponible]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }




    public function seleccionarDireccionParaOrdenes(Request $request){

        $reglaDatos = array(
            'dirid' => 'required',
            'id' => 'required', // id cliente
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        if(Clientes::where('id', $request->id)->first()){

            if($infoDireCliente = DireccionCliente::where('id_cliente', $request->id)
                ->where('id', $request->dirid)->first()){

                DB::beginTransaction();

                try {

                    // setear a 0 todas las direcciones del cliente
                    DireccionCliente::where('id_cliente', $request->id)->update(['seleccionado' => 0]);

                    // setear a 1 el id de la direccion que envia el usuario
                    DireccionCliente::where('id', $request->dirid)->update(['seleccionado' => 1]);


                    $infoZonaServicio = ZonasServicio::where('id_zonas', $infoDireCliente->id_zonas)->first();


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

                    // BORRAR EL PREMIO QUE TENGA SELECCIONADO

                    ClientesPremios::where('id_clientes', $request->id)->delete();



                    DB::commit();

                    // direccion seleccionda
                    return ['success' => 1];

                }catch(\Throwable $e){
                    Log::info('ee' .$e);
                    DB::rollback();
                    // error
                    return ['success' => 99];
                }

            }else{
                // cliente no encontrado
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }



    public function eliminarDireccionSeleccionadaCliente(Request $request){

        $reglaDatos = array(
            'id' => 'required',
            'dirid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        if($infoDire = DireccionCliente::where('id', $request->dirid)
            ->where('id_cliente', $request->id)->first()){

            DB::beginTransaction();

            try {

                $countDireccionCliente = DireccionCliente::where('id_cliente', $request->id)->count();

                if($countDireccionCliente > 1){

                    // verificar si esta direccion era la que estaba seleccionada, para poner una aleatoria
                    $infoDireccionCliente = DireccionCliente::where('id', $infoDire->id)->first();

                    // borrar direccion
                    DireccionCliente::where('id', $infoDire->id)->delete();

                    // si era la seleccionada poner aleatoria, sino no hacer nada
                    if($infoDireccionCliente->seleccionado == 1){

                        // volver a buscar la primera linea y poner seleccionado
                        $infoDireccionNueva = DireccionCliente::where('id_cliente', $request->id)->first();
                        DireccionCliente::where('id', $infoDireccionNueva->id)->update(['seleccionado' => 1]);
                    }

                     DB::commit();

                    // BORRADA CORRECTAMENTE
                    return ['success' => 1];
                }else{

                    // NO SE PUEDE ELIMINAR LA DIRECCION
                    return ['success' => 2];
                }
            }catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3];
            }
        }else{
            return ['success' => 3];
        }
    }




    public function notaProblemaAplicacion(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'problema' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        DB::beginTransaction();

        try {

            $fecha = Carbon::now('America/El_Salvador');


            $dato = new ReporteProblema();
            $dato->id_cliente = $request->clienteid;
            $dato->manufactura = $request->manufactura;
            $dato->nombre = $request->nombre;
            $dato->modelo = $request->modelo;
            $dato->codenombre = $request->codenombre;
            $dato->devicenombre = $request->devicenombre;
            $dato->problema = $request->problema;
            $dato->fecha = $fecha;
            $dato->save();


            DB::commit();

            return ['success' => 1];

        } catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }

    }









    //***********************************************************************************


    public function loginRestaurante(Request $request){


        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        // idfirebase

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($info = UsuariosServicios::where('usuario', $request->usuario)->first()){


            // EL USUARIO PUEDE ESTAR BLOQUEADO

            if($info->bloqueado == 1){

                $titulo = 'Nota';
                $mensaje = "Su usuario ha sido bloqueado";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            if (Hash::check($request->password, $info->password)) {

                if($request->idfirebase != null){
                    UsuariosServicios::where('id', $info->id)->update(['token_fcm' => $request->idfirebase]);
                }

                // inicio sesion
                return ['success' => 2, 'id' => strval($info->id), 'mensaje' => "Inicio de sesion correctamente"];

            }else{
                // contraseña incorrecta

                $titulo = 'Nota';
                $mensaje = "Su Contraseña es incorrecta";

                return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

        } else {

            $titulo = 'Nota';
            $mensaje = "Usuario no encontrado";

            // usuario no encontrado
            return ['success' => 4, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }
    }








    public function loginMotorista(Request $request){


        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        // idfirebase

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($info = MotoristasServicios::where('usuario', $request->usuario)->first()){


            // EL USUARIO PUEDE ESTAR BLOQUEADO

            if($info->bloqueado == 1){

                $titulo = 'Nota';
                $mensaje = "Su usuario ha sido bloqueado";

                return ['success' => 1, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

            if (Hash::check($request->password, $info->password)) {

                if($request->idfirebase != null){
                    MotoristasServicios::where('id', $info->id)
                        ->update(['token_fcm' => $request->idfirebase,
                            'notificacion' => 1]);
                }

                // inicio sesion
                return ['success' => 2, 'id' => strval($info->id), 'mensaje' => "Inicio de sesion correctamente"];

            }else{
                // contraseña incorrecta

                $titulo = 'Nota';
                $mensaje = "Su Contraseña es incorrecta";

                return ['success' => 3, 'titulo' => $titulo, 'mensaje' => $mensaje];
            }

        } else {

            $titulo = 'Nota';
            $mensaje = "Usuario no encontrado";

            // usuario no encontrado
            return ['success' => 4, 'titulo' => $titulo, 'mensaje' => $mensaje];
        }
    }


    // ELIMINACION TOTAL DEL CLIENTE
    public function eliminacionTotalCliente(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ($validarDatos->fails()) {
            return ['success' => 0];
        }


        if($cl = Clientes::where('id', $request->clienteid)->first()){

            // DELETE ALL THE CLIENTE BY ID

            Clientes::where('id', $cl->id)
                ->update(['activo' => 0]);

            return ['success' => 1];

        }else{
            return ['success' => 2];
        }
    }















}
