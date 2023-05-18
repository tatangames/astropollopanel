<?php

namespace App\Http\Controllers\Frontend\Login;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Mail\CorreoPasswordMail;


class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest', ['except' => ['logout']]);
    }

    // retorna vista de login
    public function index(){
        return view('frontend.login.vistalogin');
    }


    public function enviarCorreoTest(){


        $codigo = '';
        for($i = 0; $i < 6; $i++) {
            $codigo .= mt_rand(0, 9);
        }

        $usuario = "pepe";

        $data = ["usuario" => $usuario,
                "codigo" => $codigo];

        Mail::to("tatangamess@gmail.com")
            ->send(new CorreoPasswordMail($data));

        return ['success' => 2];
    }


    // verificar usuario y contraseña para iniciar sesión
    public function login(Request $request){

        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // si ya habia iniciado sesión, redireccionar
        if (Auth::check()) {
            return ['success'=> 1, 'ruta'=> route('admin.panel')];
        }

        if($info = Administrador::where('usuario', $request->usuario)->first()){

            if($info->activo == 0){
                return ['success' => 5];
            }

            if(Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password])) {

                return ['success'=> 1, 'ruta'=> route('admin.panel')];
            }else{
                return ['success' => 2]; // password incorrecta
            }
        }else{
            return ['success' => 3]; // usuario no encontrado
        }
    }

    // cerrar sesión
    public function logout(Request $request){
        Auth::logout();
        return redirect('/');
    }
}
