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


// *** ENVIO DE CORREO PRUEBA

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

// --- SLIDER de servicios ---
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



// --- PRODUCTOS POPULARES DEL SERVICIO ----
Route::get('/admin/productos/servicio/principales/{id}', [CategoriasController::class,'indexServiciosProductosPrincipales']);
Route::get('/admin/productos/servicio/principales/tabla/{id}', [CategoriasController::class,'tablaServiciosProductosPrincipales']);
Route::post('/admin/productos/servicio/principales/nuevo', [CategoriasController::class,'nuevoProductosPrincipales']);
Route::post('/admin/productos/servicio/principales/borrar', [CategoriasController::class,'borrarProductosPrincipales']);


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


// --- TODAS LAS ORDENES JUNTAS ---







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





// CALL CENTER

Route::get('/admin/callcenter/generar/orden', [CallCenterController::class,'indexGenerarOrden'])->name('index.callcenter.generarorden');
Route::get('/admin/callcenter/generar/orden/direcciones', [CallCenterController::class,'listaDireccionTelefono']);




























