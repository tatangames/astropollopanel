<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\ApiRegistroController;
use App\Http\Controllers\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Api\Cliente\ApiDireccionesController;
use App\Http\Controllers\Api\Cliente\ApiMenuController;
use App\Http\Controllers\Api\Carrito\CarritoComprasController;


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

// retorna lista de todas las categorias del servicio (filtro horario)
Route::post('cliente/listado/todas/categorias', [ApiMenuController::class, 'listaDeTodasLasCategorias']);

// retorna listado de productos cuando es seleccionada una categoria
Route::post('cliente/listado/productos/servicios', [ApiMenuController::class, 'listaDeTodosLosProductosServicio']);

// retorna informacion de 1 producto individual
Route::post('cliente/informacion/producto/individual', [ApiMenuController::class, 'informacionProductoIndividual']);

// agregar el producto al carrito de compras del cliente
Route::post('cliente/carrito/producto/agregar', [CarritoComprasController::class, 'agregarProductoCarritoTemporal']);




