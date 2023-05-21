<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\ApiRegistroController;
use App\Http\Controllers\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Api\Cliente\ApiDireccionesController;
use App\Http\Controllers\Api\Cliente\ApiMenuController;
use App\Http\Controllers\Api\Carrito\CarritoComprasController;
use App\Http\Controllers\Api\Procesar\ApiProcesarController;
use App\Http\Controllers\Api\Ordenes\ApiOrdenesController;
use App\Http\Controllers\Api\Ordenes\ApiOrdenesRestauranteController;


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
Route::post('cliente/proceso/enviar/orden', [ApiProcesarController::class, 'enviarOrdenRestaurante']);



// LISTADO DE ORDENES ACTIVAS DEL CLIENTE
Route::post('cliente/ordenes/listado/activas', [ApiOrdenesController::class, 'verListadoOrdenesActivasCliente']);

// VER ESTADO DE ORDEN INDIVIDUAL

Route::post('cliente/orden/informacion/estado',  [ApiOrdenesController::class, 'informacionOrdenIndividual']);


// ver motorista de la orden
Route::post('cliente/orden/ver/motorista',  [ApiOrdenesController::class, 'verMotoristaOrden']);


// calificar orden y completarla
Route::post('cliente/orden/completar/calificacion',  [ApiOrdenesController::class, 'calificarLaOrden']);


// listado de productos de una orden
Route::post('cliente/listado/productos/ordenes',  [ApiOrdenesController::class, 'listadoProductosOrdenes']);

// informacion de producto individual de una orden que se pidio
Route::post('cliente/listado/productos/ordenes-individual',  [ApiOrdenesController::class, 'infoProductoOrdenadoIndividual']);














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







// *********************** RUTAS PARA APLICACION DE RESTAURANTES ****************************************



Route::post('restaurante/login', [ApiClienteController::class, 'loginRestaurante']);

// listado de ordenes nuevas, filtrado por restaurantes
Route::post('restaurante/nuevas/ordenes', [ApiOrdenesRestauranteController::class, 'nuevasOrdenes']);

// listado de productos de una orden
Route::post('restaurante/listado/producto/orden', [ApiOrdenesRestauranteController::class, 'listadoProductosOrden']);

// informacion de producto individual de una orden que se pidio
Route::post('restaurante/listado/productos/ordenes-individual',  [ApiOrdenesRestauranteController::class, 'infoProductoOrdenadoIndividual']);

// INICIAR ORDEN -> NOTIFICACION ONE SIGNAL A CLIENTE

Route::post('restaurante/proceso/orden/iniciar-orden',  [ApiOrdenesRestauranteController::class, 'iniciarOrdenPorRestaurante']);

// CANCELAR ORDEN AL CLIENTE -> NOTIFICACION ONE SIGNAL AL CLIENTE

Route::post('restaurante/cancelar/orden', [ApiOrdenesRestauranteController::class, 'cancelarOrden']);



// LISTADO DE ORDENES QUE YA ESTAN EN PREPARACION

Route::post('restaurante/preparacion/ordenes', [ApiOrdenesRestauranteController::class, 'preparacionOrdenes']);
















