<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\ApiRegistroController;
use App\Http\Controllers\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Api\Cliente\ApiDireccionesController;
use App\Http\Controllers\Api\Cliente\ApiMenuController;
use App\Http\Controllers\Api\Carrito\ApiCarritoComprasController;
use App\Http\Controllers\Api\Procesar\ApiProcesarController;
use App\Http\Controllers\Api\Ordenes\ApiOrdenesController;
use App\Http\Controllers\Api\Ordenes\ApiOrdenesRestauranteController;
use App\Http\Controllers\Api\Ordenes\ApiOrdenesMotoristaController;


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
Route::post('cliente/carrito/producto/agregar', [ApiCarritoComprasController::class, 'agregarProductoCarritoTemporal']);

// ver carrito de compras
Route::post('cliente/carrito/ver/orden', [ApiCarritoComprasController::class, 'verCarritoDeCompras']);

// borrar carrito de compras
Route::post('cliente/carrito/borrar/orden', [ApiCarritoComprasController::class, 'borrarCarritoDeCompras']);

// eliminar una fila del carrito de compras
Route::post('cliente/carrito/eliminar/producto', [ApiCarritoComprasController::class, 'borrarProductoDelCarrito']);

// ver producto individual en pantalla de editar la cantidad

Route::post('cliente/carrito/ver/producto', [ApiCarritoComprasController::class, 'verProductoCarritoEditar']);

// cambiar la cantidad de producto a editar en carrito de compras
Route::post('cliente/carrito/cambiar/cantidad', [ApiCarritoComprasController::class, 'editarCantidadProducto']);

// informacion final para procesar la orden
Route::post('cliente/carrito/ver/proceso-orden', [ApiCarritoComprasController::class, 'verOrdenAProcesarCliente']);

// ** verificacion de cupones **
Route::post('cliente/verificar/cupon', [ApiCarritoComprasController::class, 'verificarCupon']);




// ***********   ENVIO DE LA ORDEN DEL CLIENTE************
Route::post('cliente/proceso/enviar/orden', [ApiProcesarController::class, 'enviarOrdenRestaurante']);

// ENVIO NOTIFICACION DESPUES DE CONFIRMAR ORDEN
Route::post('cliente/proceso/orden/notificacion', [ApiProcesarController::class, 'notificacionOrdenParaRestaurante']);


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


// CANCELAR ORDEN POR EL CLIENTE
Route::post('cliente/proceso/cancelar/orden', [ApiOrdenesController::class, 'cancelarOrdenPorCliente']);



// HISTORIAL DE ORDENES POR EL CLIENTE
Route::post('cliente/historial/listado/ordenes', [ApiOrdenesController::class, 'historialOrdenesCliente']);


//  OCULTARME MI ORDEN PORQUE FUE CANCELADA
Route::post('cliente/ocultar/mi/orden', [ApiOrdenesController::class, 'ocultameOrdenCliente']);








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


// FINALIZAR ORDEN -> NOTIFICACION SIGNAL A MOTORISTA QUE TIENE LA ORDEN
                   // O ENVIA NOTIFICACION A TODOS LOS MOTORISTAS PARA AGARRARLA
Route::post('restaurante/proceso/orden/finalizar-orden',  [ApiOrdenesRestauranteController::class, 'finalizarOrdenPorRestaurante']);


// LISTADO DE ORDENES QUE SE HAN COMPLETADO HOY
Route::post('restaurante/completadashoy/ordenes', [ApiOrdenesRestauranteController::class, 'completadasHoyOrdenes']);


// LISTADO DE ORDENES QUE SE HAN CANCELADO HOY
Route::post('restaurante/canceladashoy/ordenes', [ApiOrdenesRestauranteController::class, 'canceladasHoyOrdenes']);


// LISTADO DE CATEGORIAS DEL RESTAURANTE
Route::post('restaurante/listado/categorias', [ApiOrdenesRestauranteController::class, 'listadoDeCategorias']);

// ACTUALIZAR ESTADO DE CATEGORIA
Route::post('restaurante/actualizar/estado/categorias', [ApiOrdenesRestauranteController::class, 'actualizarEstadoCategoria']);


// LISTADO DE PRODUCTOS POR CATEGORIA DEL RESTAURANTE
Route::post('restaurante/categoria/listado/productos', [ApiOrdenesRestauranteController::class, 'listadoDeProductosPorCategoria']);


// ACTUALIZAR ESTADO DE PRODUCTO
Route::post('restaurante/actualizar/estado/producto', [ApiOrdenesRestauranteController::class, 'actualizarEstadoProducto']);

// HISTORIAL DE ORDENES DEL RESTAURANTES
Route::post('restaurante/historial/ordenes', [ApiOrdenesRestauranteController::class, 'historialOrdenesRestaurantes']);







// *********************** RUTAS PARA APLICACION DE MOTORISTAS ****************************************


Route::post('motorista/login', [ApiClienteController::class, 'loginMotorista']);

// NUEVAS ORDENES QUE EL RESTAURANTE YA INICIO PREPARACION Y NO ESTEN CANCELADAS O AGARRADAS
Route::post('motorista/nuevas/ordenes', [ApiOrdenesMotoristaController::class, 'nuevasOrdenesMotorista']);

// VER LISTADO DE PRODUCTOS
Route::post('motorista/listado/producto/orden', [ApiOrdenesMotoristaController::class, 'listadoProductosOrden']);

// SELECCIONAR LA ORDEN POR EL MOTORISTA
Route::post('motorista/seleccionar/orden', [ApiOrdenesMotoristaController::class, 'seleccionarOrden']);

// LISTADO DE ORDENES QUE ESTAN SELECCIONAS Y PENDIENTES DE INICIAR LA ENTREGA
Route::post('motorista/pendientes/entrega/orden', [ApiOrdenesMotoristaController::class, 'pendientesEntregaOrden']);

// AQUI SE INICIA LA ENTREGA DE LA ORDEN
            // NOTIFICACION ONE SIGNAL A CLIENTE
Route::post('motorista/iniciar/entrega/orden', [ApiOrdenesMotoristaController::class, 'iniciarEntregaOrden']);

// LISTADO DE ORDENES QUE EL MOTORISTA ESTA ENTREGANDO
Route::post('motorista/entregando/entrega/orden', [ApiOrdenesMotoristaController::class, 'listadoOrdenesEstoyEntregando']);

// FINALIZAR ENTREGA POR PARTE DEL MOTORISTA
            // NOTIFICACION ONE SIGNAL AL CLIENTE
Route::post('motorista/finalizar/entrega/orden', [ApiOrdenesMotoristaController::class, 'finalizarOrden']);

// LISTADO DE ORDENES COMPLETADAS HOY POR EL MOTORISTA
Route::post('motorista/listado/completadas/hoy/orden', [ApiOrdenesMotoristaController::class, 'listadoCompletadasHoyMotorista']);


// LISTADO DE ORDENES CANCELADAS HOY Y YA ESTABAN SELECCIONADAS POR EL MOTORISTA
Route::post('motorista/listado/canceladas/hoy/orden', [ApiOrdenesMotoristaController::class, 'listadoCanceladasHoyMotorista']);


// HISTORIAL DE ORDENES PARA MOTORISTAS
Route::post('motorista/historial/ordenes', [ApiOrdenesMotoristaController::class, 'historialOrdenesMotoristas']);

// INFORMACION SI RECIBE NOTIFICACIONES
Route::post('motorista/opcion/notificacion', [ApiOrdenesMotoristaController::class, 'informacionNotificaciones']);

// EDITAR ESTADO NOTIFICACIONES MOTORISTA
Route::post('motorista/opcion/notificacion/editar', [ApiOrdenesMotoristaController::class, 'editarNotificaciones']);





//Route::post('cliente/prueba/notificaciones', [ApiOrdenesMotoristaController::class, 'enviarCorreoTest']);




