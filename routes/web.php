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

