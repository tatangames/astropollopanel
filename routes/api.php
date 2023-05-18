<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\ApiRegistroController;
use App\Http\Controllers\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Api\Cliente\ApiDireccionesController;
use App\Http\Controllers\Api\Cliente\ApiMenuController;
use App\Http\Controllers\Api\Carrito\CarritoComprasController;
use App\Http\Controllers\Api\Procesar\ProcesarController;
use App\Http\Controllers\Api\Ordenes\OrdenesController;


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
Route::post('cliente/actualizar/password', [ApiClienteController::class, 'actualizarPasswordClienteCorreo']);


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

// ver carrito de compras
Route::post('cliente/carrito/ver/orden', [CarritoComprasController::class, 'verCarritoDeCompras']);

// borrar carrito de compras
Route::post('cliente/carrito/borrar/orden', [CarritoComprasController::class, 'borrarCarritoDeCompras']);

// eliminar una fila del carrito de compras
Route::post('cliente/carrito/eliminar/producto', [CarritoComprasController::class, 'borrarProductoDelCarrito']);

// ver producto individual en pantalla de editar la cantidad

Route::post('cliente/carrito/ver/producto', [CarritoComprasController::class, 'verProductoCarritoEditar']);

// cambiar la cantidad de producto a editar en carrito de compras
Route::post('cliente/carrito/cambiar/cantidad', [CarritoComprasController::class, 'editarCantidadProducto']);

// informacion final para procesar la orden
Route::post('cliente/carrito/ver/proceso-orden', [CarritoComprasController::class, 'verOrdenAProcesarCliente']);

// ** verificacion de cupones **
Route::post('cliente/verificar/cupon', [CarritoComprasController::class, 'verificarCupon']);




// ***********   ENVIO DE LA ORDEN DEL CLIENTE************
Route::post('cliente/proceso/enviar/orden', [ProcesarController::class, 'enviarOrdenRestaurante']);






// informacion del cliente
Route::post('cliente/informacion/personal', [ApiClienteController::class, 'informacionCliente']);

// informacion horario del restaurante segun direccion
Route::post('cliente/informacion/restaurante/horario', [ApiClienteController::class, 'informacionHorarioRestaurante']);

// actualizar contrasena del cliente
Route::post('cliente/perfil/actualizar/contrasena', [ApiClienteController::class, 'actualizarPasswordClientePerfil']);

// ver informacion si el cliente borrar carrito de compras al hacer una orden o no
Route::post('cliente/opcion/perfil/carrito', [ApiClienteController::class, 'infoBorrarCarritoComprasCliente']);

// guardar opcion si borrar carrito al hacer una orden o no
Route::post('cliente/opcion/perfil/carrito/guardar', [ApiClienteController::class, 'actualizarOpcionCarritoCliente']);

// elegir la direccion seleccionada por el cliente
Route::post('cliente/direcciones/elegir/direccion', [ApiClienteController::class, 'seleccionarDireccionParaOrdenes']);

// eliminar direccion seleccionada
Route::post('cliente/eliminar/direccion/seleccionada', [ApiClienteController::class, 'eliminarDireccionSeleccionadaCliente']);


// LISTADO DE ORDENES ACTIVAS DEL CLIENTE
Route::post('cliente/ordenes/listado/activas', [OrdenesController::class, 'verListadoOrdenesActivasCliente']);

// VER ESTADO DE ORDEN INDIVIDUAL

Route::post('cliente/orden/informacion/estado',  [OrdenesController::class, 'informacionOrdenIndividual']);
















