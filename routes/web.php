<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Configuracion\ZonasController;
use App\Http\Controllers\Backend\Configuracion\ServiciosController;
use App\Http\Controllers\Backend\Configuracion\CategoriasController;
use App\Http\Controllers\Backend\Configuracion\ZonasServicioController;
use App\Http\Controllers\Backend\Configuracion\ProductosController;
use App\Http\Controllers\Backend\Configuracion\SliderController;
use App\Http\Controllers\Backend\Configuracion\CuponesController;
use App\Http\Controllers\Backend\Clientes\ClientesController;
use App\Http\Controllers\Backend\Ordenes\OrdenesController;
use App\Http\Controllers\Backend\CallCenter\CallCenterController;
use App\Http\Controllers\Backend\CallCenter\CallCenterDireccionesController;
use App\Http\Controllers\Backend\CallCenter\CallCenterOrdenesController;
use App\Http\Controllers\Backend\Configuracion\NotificacionesController;
use App\Http\Controllers\Backend\Reportes\ReportesController;



Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('admin/login', [LoginController::class, 'login']);
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---

Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---

Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS A USUARIOS ---

Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);


// --- PERFIL ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);


// --- VISTA PARA INGRESAR CORREO ---
Route::get('/admin/ingreso/de/correo', [LoginController::class,'indexIngresoDeCorreo']);
Route::post('/admin/enviar/correo/password', [LoginController::class, 'enviarCorreoAdministrador']);


// VISTA AQUI SE INGRESA LA NUEVA CONTRASEÑA PORQUE EL LINK ES VALIDO
Route::get('/admin/resetear/contrasena/administrador/{token}', [LoginController::class,'indexIngresoNuevaPasswordLink']);

// VISTA SIN TOKEN PARA REDIRECCION
Route::get('/admin/resetear/contrasena/administrador', [LoginController::class,'indexIngresoNuevaPasswordLinkRedireccion']);


// ACTUALIZACION DE CONTRASENA

Route::post('/admin/administrador/actualizacion/password', [LoginController::class, 'actualizarPasswordAdministrador']);








// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');


// --- ZONAS ---
Route::get('/admin/zonas/mapa/zona', [ZonasController::class,'index'])->name('index.vistas.zonas');
Route::get('/admin/zonas/tablas/zona', [ZonasController::class,'tablaZonas']);
Route::post('/admin/zonas/registro/nueva', [ZonasController::class,'nuevaZona']);
Route::post('/admin/zonas/informacion-zona', [ZonasController::class,'informacionZona']);
Route::post('/admin/zonas/editar-zona', [ZonasController::class,'editarZona']);
Route::get('/admin/zonas/ver-mapa/{id}', [ZonasController::class,'verMapa']);


// --- POLIGONO ---
Route::get('/admin/zonas/poligono/{id}', [ZonasController::class,'indexPoligono']);
Route::post('/admin/zonas/poligono/listado-nuevo', [ZonasController::class,'nuevoPoligono']);
Route::post('/admin/zonas/poligono/borrar', [ZonasController::class,'borrarPoligonos']);

// --- SERVICIOS ---
Route::get('/admin/servicios/listado', [ServiciosController::class,'index'])->name('index.servicios.listado');
Route::get('/admin/servicios/listado/tabla', [ServiciosController::class,'serviciosTabla']);
Route::post('/admin/servicios/registrar/nuevo', [ServiciosController::class,'registrarServicio']);
Route::post('/admin/servicios/informacion', [ServiciosController::class,'informacionServicio']);
Route::post('/admin/servicios/editar-servicio', [ServiciosController::class,'editarServicios']);
Route::post('/admin/servicios/informacion-horario/servicio', [ServiciosController::class,'informacionHorarios']);
Route::post('/admin/servicios/editar/horarios', [ServiciosController::class,'editarHorarioServicio']);




// --- CATEGORIAS de servicio---
Route::get('/admin/categorias/listado/{id}', [CategoriasController::class,'index']);
Route::get('/admin/categorias/listado/tabla/{id}', [CategoriasController::class,'categoriasTabla']);
Route::post('/admin/categorias/nuevo', [CategoriasController::class,'nuevaCategorias']);
Route::post('/admin/categorias/informacion', [CategoriasController::class,'informacionCategorias']);
Route::post('/admin/categorias/editar', [CategoriasController::class,'editarCategorias']);
Route::post('/admin/categorias/ordenar', [CategoriasController::class,'ordenarCategorias']);




// --- SUB CATEGORIAS de servicio---
Route::get('/admin/sub/categorias/listado/{id}', [CategoriasController::class,'indexSubCategorias']);
Route::get('/admin/sub/categorias/listado/tabla/{id}', [CategoriasController::class,'subCategoriasTabla']);
Route::post('/admin/sub/categorias/nuevo', [CategoriasController::class,'nuevaSubCategorias']);
Route::post('/admin/sub/categorias/informacion', [CategoriasController::class,'informacionSubCategorias']);
Route::post('/admin/sub/categorias/editar', [CategoriasController::class,'editarSubCategorias']);
Route::post('/admin/sub/categorias/ordenar', [CategoriasController::class,'ordenarSubCategorias']);





/// --- ZONAS SERVICIO ---
Route::get('/admin/zonasservicio/listado', [ZonasServicioController::class,'index'])->name('index.zonas.servicio.listado');
Route::get('/admin/zonasservicio/listado/tabla', [ZonasServicioController::class,'zonasServicioTablas']);
Route::post('/admin/zonaservicios/nuevo', [ZonasServicioController::class,'nuevaZonaServicio']);
Route::post('/admin/zonaservicios/borrar', [ZonasServicioController::class,'borrarRegistro']);


// --- PRODUCTOS ---
Route::get('/admin/productos/listado/{id}', [ProductosController::class,'index']);
Route::get('/admin/productos/listado/tabla/{id}', [ProductosController::class,'productosTabla']);
Route::post('/admin/productos/nuevo', [ProductosController::class,'nuevoProducto']);
Route::post('/admin/productos/informacion', [ProductosController::class,'informacionProductos']);
Route::post('/admin/productos/editar', [ProductosController::class,'editarProductos']);
Route::post('/admin/productos/ordenar', [ProductosController::class,'ordenarProductos']);

// --- BANNER de restaurante ---
Route::get('/admin/slider/listado/{id}', [SliderController::class,'index']);
Route::get('/admin/slider/listado/tabla/{id}', [SliderController::class,'sliderTabla']);
Route::post('/admin/slider/nuevo', [SliderController::class,'nuevaSlider']);
Route::post('/admin/slider/borrar', [SliderController::class,'borrarSliders']);
Route::post('/admin/slider/informacion', [SliderController::class,'informacionSlider']);
Route::post('/admin/slider/editar', [SliderController::class,'editarSlider']);
Route::post('/admin/slider/ordenar', [SliderController::class,'ordenarSlider']);

// --- CUPONES ---
Route::get('/admin/cupones/listado', [CuponesController::class,'index'])->name('index.cupones.listado');
Route::get('/admin/cupones/listado/tabla', [CuponesController::class,'cuponesTabla']);
Route::post('/admin/cupones/nuevo', [CuponesController::class,'nuevoRegistro']);

Route::post('/admin/cupones/informacion', [CuponesController::class,'informacionCupon']);
Route::post('/admin/cupones/editar', [CuponesController::class,'editarCupon']);

// --- CUPONES ASIGNADOS A SERVICIOS (CUPON PRODUCTO GRATIS)---
Route::get('/admin/cupones/servicio/progratis/{id}', [CuponesController::class,'indexServiciosCuponProGratis']);
Route::get('/admin/cupones/servicio/progratis/tabla/{id}', [CuponesController::class,'tablaServiciosCuponProGratis']);
Route::post('/admin/cupones/servicio/progratis/nuevo', [CuponesController::class,'nuevoCuponProGratis']);
Route::post('/admin/cupones/servicio/progratis/borrar', [CuponesController::class,'borrarCuponProGratis']);


// --- CUPONES ASIGNADOS A SERVICIOS (CUPON DESCUENTO DE DINERO)---
Route::get('/admin/cupones/servicio/descdinero/{id}', [CuponesController::class,'indexServiciosCuponDescuentoDinero']);
Route::get('/admin/cupones/servicio/descdinero/tabla/{id}', [CuponesController::class,'tablaServiciosCuponDescuentoDinero']);
Route::post('/admin/cupones/servicio/descdinero/nuevo', [CuponesController::class,'nuevoCuponDescuentoDinero']);
Route::post('/admin/cupones/servicio/descdinero/borrar', [CuponesController::class,'borrarCuponDescuentoDinero']);


// --- CUPONES ASIGNADOS A SERVICIOS (CUPON DESCUENTO DE PORCENTAJE)---
Route::get('/admin/cupones/servicio/descporcentaje/{id}', [CuponesController::class,'indexServiciosCuponDescuentoPorcentaje']);
Route::get('/admin/cupones/servicio/descporcentaje/tabla/{id}', [CuponesController::class,'tablaServiciosCuponDescuentoPorcentaje']);
Route::post('/admin/cupones/servicio/descporcentaje/nuevo', [CuponesController::class,'nuevoCuponDescuentoPorcentaje']);
Route::post('/admin/cupones/servicio/descporcentaje/borrar', [CuponesController::class,'borrarCuponDescuentoPorcentaje']);


// --- CATEGORIAS PRINCIPALES DEL SERVICIO ---
Route::get('/admin/categorias/servicio/principales/{id}', [CategoriasController::class,'indexServiciosCuponDescuentoDinero']);
Route::get('/admin/categorias/servicio/principales/tabla/{id}', [CategoriasController::class,'tablaServiciosCuponDescuentoDinero']);
Route::post('/admin/categorias/servicio/principales/nuevo', [CategoriasController::class,'nuevoCategoriaPrincipal']);
Route::post('/admin/categorias/servicio/principales/borrar', [CategoriasController::class,'borrarCategoriaPrincipal']);
Route::post('/admin/categorias/servicio/principales/ordenar', [CategoriasController::class,'ordenarCategoriaPrincipal']);



// --- PRODUCTOS POPULARES DEL SERVICIO ----
Route::get('/admin/productos/servicio/principales/{id}', [CategoriasController::class,'indexServiciosProductosPrincipales']);
Route::get('/admin/productos/servicio/principales/tabla/{id}', [CategoriasController::class,'tablaServiciosProductosPrincipales']);
Route::post('/admin/productos/servicio/principales/nuevo', [CategoriasController::class,'nuevoProductosPrincipales']);
Route::post('/admin/productos/servicio/principales/borrar', [CategoriasController::class,'borrarProductosPrincipales']);
Route::post('/admin/productos/servicio/principales/ordenar', [CategoriasController::class,'ordenarProductosPopulares']);





// --- LISTA DE CLIENTES REGISTRADOS ----
Route::get('/admin/clientes/listado', [ClientesController::class,'index'])->name('index.clientes.listado');
Route::get('/admin/clientes/listado/tabla', [ClientesController::class,'tablaClientes']);
Route::post('/admin/clientes/listado/informacion', [ClientesController::class,'informacionCliente']);
Route::post('/admin/clientes/informacion/editar', [ClientesController::class,'editarCliente']);


// --- LISTA DE DIRECCIONES DEL CLIENTE ---
Route::get('/admin/clientes/direcciones/listado/{id}', [ClientesController::class,'indexListaDirecciones']);
Route::get('/admin/clientes/direcciones/listado/tabla/{id}', [ClientesController::class,'tablaClientesDirecciones']);

// revisar si direccion tiene coordenadas reales donde se registro
Route::post('/admin/clientes/tiene/gps/coordenadas', [ClientesController::class,'infoCoordenadasReales']);


Route::get('/admin/clientes/direcciones/mapa/registrado/{id}', [ClientesController::class,'mapaDireccionRegistrado']);
Route::get('/admin/clientes/direcciones/mapa/real/{id}', [ClientesController::class,'mapaDireccionReal']);


// ***********  ORDENES **************


// --- ORDENES PENDIENTES DE CONTESTACION ----
Route::get('/admin/ordenes/pendientes/listado', [OrdenesController::class,'indexOrdenesPendientes'])->name('index.ordenes.pendientes');
Route::get('/admin/ordenes/pendientes/listado/tabla', [OrdenesController::class,'tablaOrdenesPendientes']);
Route::post('/admin/ordenes/pendientes/infocliente', [OrdenesController::class,'informacionClienteOrden']);
Route::get('/admin/ordenes/pendientes/mapa/{id}', [OrdenesController::class,'mapaDireccionRegistrado']);


// --- ORDENES INICIADAS HOY ---
Route::get('/admin/ordenes/iniciadashoy/listado', [OrdenesController::class,'indexOrdenesIniciadasHoy'])->name('index.ordenes.iniciadas.hoy');
Route::get('/admin/ordenes/iniciadashoy/listado/tabla', [OrdenesController::class,'tablaOrdenesIniciadasHoy']);

Route::post('/admin/ordenes/iniciadashoy/infoproceso', [OrdenesController::class,'informacionProcesoOrdenIniciadas']);


// --- ORDENES CANCELADAS HOY ---

Route::get('/admin/ordenes/canceladashoy/listado', [OrdenesController::class,'indexOrdenesCanceladasHoy'])->name('index.ordenes.canceladas.hoy');
Route::get('/admin/ordenes/canceladashoy/listado/tabla', [OrdenesController::class,'tablaOrdenesCanceladasHoy']);


// --- PRODUCTOS DE LA ORDEN ---

Route::get('/admin/ordenes/productos/listado/{id}', [OrdenesController::class,'indexListaProductosOrdenes']);
Route::get('/admin/ordenes/productos/listado/tabla/{id}', [OrdenesController::class,'tablaProductosOrdenes']);


// --- TODAS LAS ORDENES ---
Route::get('/admin/ordenes/todas/listado', [OrdenesController::class,'indexTodasLasOrdenes'])->name('index.todas.las.ordenes');
Route::get('/admin/ordenes/todas/listado/tabla', [OrdenesController::class,'tablaTodasLasOrdenes']);



// --- CALIFICACIONES DE ORDENES ---
Route::get('/admin/ordenes/califificaciones/listado', [OrdenesController::class,'indexListaCalificacionesOrden'])->name('index.ordenes.calificadas');
Route::get('/admin/ordenes/califificaciones/tabla', [OrdenesController::class,'tablaListaCalificacionesOrden']);



// --- USUARIOS PARA MANEJAR EL RESTAURANTE ---

Route::get('/admin/restaurantes/usuario', [ServiciosController::class,'indexUsuariosRestaurantes'])->name('index.usuarios.restaurantes');
Route::get('/admin/restaurantes/usuario/tabla', [ServiciosController::class,'tablaUsuariosRestaurantes']);
Route::post('/admin/restaurantes/usuario/nuevo', [ServiciosController::class,'registrarUsuarioRestaurante']);
Route::post('/admin/restaurantes/usuario/bloquear', [ServiciosController::class,'bloquearUsuarioRestaurante']);
Route::post('/admin/restaurantes/usuario/informacion', [ServiciosController::class,'informacionUsuarioRestaurante']);
Route::post('/admin/restaurantes/usuario/actualizar', [ServiciosController::class,'actualizarUsuarioRestaurante']);


// --- MOTORISTAS PARA LOS RESTAURANTES ---

Route::get('/admin/motoristas/usuario', [ServiciosController::class,'indexMotoristasRestaurantes'])->name('index.motoristas.restaurantes');
Route::get('/admin/motoristas/usuario/tabla', [ServiciosController::class,'tablaMotoristasRestaurantes']);
Route::post('/admin/motoristas/usuario/nuevo', [ServiciosController::class,'registrarMotoristaRestaurante']);
Route::post('/admin/motoristas/usuario/informacion', [ServiciosController::class,'informacionMotoristaRestaurante']);
Route::post('/admin/motoristas/usuario/editar', [ServiciosController::class,'actualizarMotoristaRestaurante']);

// informacion de las ordenes entregas por los motoristas





// --- NOTIFICACIONES ---
Route::get('/admin/notificaciones/porrestaurantes', [NotificacionesController::class,'indexNotificacionPorRestaurante'])->name('index.notificaciones.restaurantes');
Route::post('/admin/notificaciones/enviar/porservicio', [NotificacionesController::class,'enviarNotificacionPorServicio']);


// VISTA BUSCAR UNA DIRECCION Y ESE CLIENTE ENVIARLA NOTIFICACION
Route::get('/admin/notificaciones/vista/porcliente', [NotificacionesController::class,'indexListaDireccioneNotificacion'])->name('index.notificaciones.porcliente');
Route::get('/admin/notificaciones/vista/porcliente/tabla', [NotificacionesController::class,'tablaClientesDireccionesNotificacion']);
Route::post('/admin/notificacion/cliente/informacion', [NotificacionesController::class,'informacionCliente']);
Route::post('/admin/notificaciones/enviar/porcliente', [NotificacionesController::class,'enviarNotiPorCliente']);





// --- REPORTES ---

// ORDENES CALIFICADAS POR EL CLIENTE
Route::get('/admin/reportes/ordenes/calificadas', [ReportesController::class,'vistaReporteOrdenesCalificadas'])->name('index.reporte.ordenes.calificadas');
Route::get('/admin/pdf/ordenes/calificadas/{idservicio}/{desde}/{hasta}', [ReportesController::class,'pdfOrdenesCalificadas']);

// ORDENES COMPLETADAS POR EL MOTORISTA
Route::get('/admin/reportes/ordenes/entregadas', [ReportesController::class,'vistaReporteOrdenesEntregadas'])->name('index.reporte.ordenes.entregadas');
Route::post('/admin/reportes/buscar/motorista', [ReportesController::class,'buscarMotoristaPorRestaurante']);
Route::get('/admin/pdf/ordenes/entregadas/{idmotorista}/{idservicio}/{desde}/{hasta}', [ReportesController::class,'pdfOrdenesEntregadas']);



































// ******************************     CALL CENTER **********************************




Route::get('/admin/callcenter/generar/orden', [CallCenterController::class,'indexGenerarOrden'])->name('index.callcenter.generarorden');

// buscar numero telefonico para ver sus direcciones guardadas
Route::post('/admin/callcenter/buscar/numero', [CallCenterController::class,'informacionClientePorNumero']);

// GUARDAR DIRECCION CLIENTE, BORRAR CARRITO DE COMPRAS PORQUE ESTE ESTARA SELECCIONADO
Route::post('/admin/callcenter/guardar/nueva/direccion', [CallCenterController::class,'nuevaDireccionCliente']);


// DEVUELVE LISTADO DE PRODUCTOS DE UN RESTAURANTE, DIRECCION ASIGNADA, CARRITO DE COMPRAS
Route::get('/admin/callcenter/todo/restaurante/asignado', [CallCenterController::class,'todoMenuRestauranteyCarrito']);

// CREAR CARRITO DE COMPRAS CON LA DIRECCION SELECCIONADA Y BORRAR CARRITO SI HABIA
Route::post('/admin/callcenter/seleccionar/direccion', [CallCenterController::class,'seleccionarDireccionCliente']);

// DEVOLVER TODOS LOS PRODUCTOS POR ID CATEGORIA
Route::get('/admin/callcenter/categoria/productos/{idcate}', [CallCenterController::class,'listadoProductosPorCategoria']);

// INFORMACION PRODUCTO PARA AGREGAR AL CARRITO DE COMPRAS
Route::post('/admin/callcenter/informacion/producto', [CallCenterController::class,'informacionProducto']);

// GUARDAR PRODUCTO EN CARRITO DE COMPRAS
Route::post('/admin/callcenter/guardar/producto/carrito', [CallCenterController::class,'guardarProductoEnCarrito']);

// BORRAR FILA DE PRODUCTO DEL CARRITO DE COMPRAS
Route::post('/admin/callcenter/borrar/producto/carrito', [CallCenterController::class,'borrarFilaProducto']);

// RECARGA TABLA DE CARRITO DE COMPRAS
Route::get('/admin/callcenter/recargar/tabla/carrito', [CallCenterController::class,'recargarTablaCarrito']);


// BORRAR PRODUCTOS Y CARRITO DE COMPRA Y DESELECCIONA DIRECCION
Route::post('/admin/callcenter/borrar/todoel/carrito', [CallCenterController::class,'borrarYDeseleccionarTodo']);

// INFORMACION DE UNA FILA DEL CARRITO DE COMPRAS
Route::post('/admin/callcenter/informacion/producto/carrito', [CallCenterController::class,'informacionProductoFilaCarrito']);

// ACTUALIZAR FILA DE CARRITO DE COMPRAS
Route::post('/admin/callcenter/actualizar/fila/carrito', [CallCenterController::class,'actualizarFilaCarritoCompras']);

// ENVIAR ORDEN FINAL POR CALL CENTER
Route::post('/admin/callcenter/enviar/orden', [CallCenterController::class,'enviarOrdenFinal']);

// ENVIAR NOTIFICACION DESPUES DE ENVIAR LA ORDEN EL DEL CALL CENTER
Route::post('/admin/callcenter/notificacion/orden', [CallCenterController::class,'notificacionARestaurante']);





// VISTA DIRECCIONES DEL CLIENTE PARA PODER EDITARLAS
Route::get('/admin/callcenter/listado/direcciones', [CallCenterDireccionesController::class,'indexListadoDirecciones'])->name('index.callcenter.listado.direcciones');
Route::get('/admin/callcenter/listado/direcciones/tabla', [CallCenterDireccionesController::class,'tablaListadoDirecciones']);
Route::post('/admin/callcenter/info/direccion/editar', [CallCenterDireccionesController::class,'informacionDireccionCallCenter']);
Route::post('/admin/callcenter/editar/direccion/cambiarrestaurante', [CallCenterDireccionesController::class,'cambiarRestauranteDireccion']);


// EDITAR LA DIRECCION
Route::post('/admin/callcenter/editar/direccion', [CallCenterDireccionesController::class,'editarDireccionCallCenter']);


// VER MIS ORDENES HOY PARA PODER CANCELAR, VER ESTADOS, VER PRODUCTOS
Route::get('/admin/callcenter/ordenes/hoy', [CallCenterOrdenesController::class,'indexListadoOrdenesHoy'])->name('index.callcenter.listado.ordenes.hoy');
Route::get('/admin/callcenter/ordenes/hoy/tabla', [CallCenterOrdenesController::class,'tablaListadoOrdenesHoy']);


// TODAS LAS ORDENES REALIZADAS POR CALL CENTER
Route::get('/admin/callcenter/ordenes/todas', [CallCenterOrdenesController::class,'indexListadoOrdenesTodas'])->name('index.callcenter.listado.ordenes.todas');
Route::get('/admin/callcenter/ordenes/todas/tabla', [CallCenterOrdenesController::class,'tablaListadoOrdenesTodas']);


// LISTADO DE DIRECCIONES DE RESTAURANTE PARA TENER REFERENCIA SI SE DA DOMICILIO A ESA DIRECCION

Route::get('/admin/callcenter/listado/direcciones/restaurante', [CallCenterDireccionesController::class,'indexListadoDireccionesRestaurante'])->name('index.callcenter.listado.direcciones.restaurante');
Route::get('/admin/callcenter/listado/direcciones/restaurante/tabla', [CallCenterDireccionesController::class,'tablaListadoDireccionesRestaurante']);
Route::post('/admin/callcenter/restaurante/direccion/nueva', [CallCenterDireccionesController::class,'nuevaDireccionParaRestaurante']);
Route::post('/admin/callcenter/restaurante/direccion/informacion', [CallCenterDireccionesController::class,'informacionDireccionRestaurante']);
Route::post('/admin/callcenter/restaurante/direccion/editar', [CallCenterDireccionesController::class,'editarDireccionRestaurante']);
Route::post('/admin/callcenter/restaurante/direccion/borrar', [CallCenterDireccionesController::class,'borrarDireccionRestaurante']);


// LISTADO DE DIRECCIONES DE CLIENTES QUE NO TIENEN DIRECCION ASIGANADA

Route::get('/admin/callcenter/listado/direcciones/sinzona', [CallCenterDireccionesController::class,'indexListadoDireccionSinzona'])->name('index.callcenter.listado.direcciones.sinzona');
Route::get('/admin/callcenter/listado/direcciones/sinzona/tabla', [CallCenterDireccionesController::class,'tablaListadoDireccionSinzona']);
Route::post('/admin/callcenter/listado/direccion/sinzona/info', [CallCenterDireccionesController::class,'infoDireccionSinZona']);
Route::post('/admin/callcenter/listado/direccion/sinzona/editar', [CallCenterDireccionesController::class,'editarDireccionSinZona']);












