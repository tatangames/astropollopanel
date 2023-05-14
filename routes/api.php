<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\ApiRegistroController;
use App\Http\Controllers\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Api\Cliente\ApiDireccionesController;
use App\Http\Controllers\Api\Cliente\ApiMenuController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// registro del cliente
Route::post('cliente/registro', [ApiRegistroController::class, 'registroCliente']);



Route::post('cliente/login', [ApiClienteController::class, 'loginCliente']);
Route::post('cliente/enviar/codigo-correo', [ApiClienteController::class, 'enviarCodigoCorreo']);
Route::post('cliente/verificar/codigo-correo-password', [ApiClienteController::class, 'verificarCodigoCorreoPassword']);
Route::post('cliente/actualizar/password', [ApiClienteController::class, 'actualizarPasswordCliente']);


// DIRECCIONES DE CLIENTE
Route::post('cliente/listado/direcciones', [ApiDireccionesController::class, 'listadoDeDirecciones']);
Route::get('cliente/listado/zonas/poligonos', [ApiDireccionesController::class, 'puntosZonaPoligonos']);

Route::post('cliente/nueva/direccion', [ApiDireccionesController::class, 'nuevaDireccionCliente']);


// MENU PRINCIPAL
Route::post('cliente/lista/servicios-bloque', [ApiMenuController::class, 'listadoMenuPrincipal']);






