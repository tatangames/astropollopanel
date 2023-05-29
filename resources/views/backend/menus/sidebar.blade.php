<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight-light">Panel Web</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


                @can('sidebar.roles.y.permisos')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Roles y Permisos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rol y Permisos</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuario</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endcan




                @can('sidebar.zonas')
                <li class="nav-item">

                    <a href="#" class="nav-link nav-">
                        <i class="far fa-edit"></i>
                        <p>
                            Zonas
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">



                        <li class="nav-item">
                            <a href="{{ route('index.vistas.zonas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Zonas</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('index.servicios.listado') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Restaurantes</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('index.zonas.servicio.listado') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Zona Restaurante</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('index.cupones.listado') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupones</p>
                            </a>
                        </li>


                    </ul>
                </li>
                @endcan



                @can('sidebar.ordenes')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Ordenes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">


                            <li class="nav-item">
                                <a href="{{ route('index.ordenes.pendientes') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Pendientes</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route('index.ordenes.iniciadas.hoy') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Iniciadas Hoy</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route('index.ordenes.canceladas.hoy') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Canceladas Hoy</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route('index.ordenes.calificadas') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Calificadas</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('index.todas.las.ordenes') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Todas las Ordenes</p>
                                </a>
                            </li>

                        </ul>
                    </li>






                @endcan




                @can('sidebar.usuarios')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Usuarios
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">


                            <li class="nav-item">
                                <a href="{{ route('index.usuarios.restaurantes') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Usuario Restaurante</p>
                                </a>
                            </li>



                            <li class="nav-item">
                                <a href="{{ route('index.motoristas.restaurantes') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Motorista Restaurante</p>
                                </a>
                            </li>



                            <li class="nav-item">
                                <a href="{{ route('index.clientes.listado') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Clientes Registrados</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan




                @can('sidebar.notificaciones')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Notificaciones
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">


                            <li class="nav-item">
                                <a href="{{ route('index.notificaciones.restaurantes') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Por Restaurante</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('index.notificaciones.porcliente') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Por Cliente</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan


                @can('sidebar.reportes')
                    <li class="nav-item">

                        <a href="#" class="nav-link nav-">
                            <i class="far fa-edit"></i>
                            <p>
                                Reportes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">


                            <li class="nav-item">
                                <a href="{{ route('index.reporte.ordenes.entregadas') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Entregadas</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route('index.reporte.ordenes.calificadas') }}" target="frameprincipal" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordenes Calificadas</p>
                                </a>
                            </li>



                        </ul>
                    </li>
                @endcan



                    @can('sidebar.callcenter')



                        <li class="nav-item">

                            <a href="#" class="nav-link nav-">
                                <i class="far fa-edit"></i>
                                <p>
                                    Ordenes
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">


                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.generarorden') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Orden</p>
                                    </a>
                                </li>



                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.listado.ordenes.hoy') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ordenes Hoy</p>
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.listado.ordenes.todas') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ordenes Todas</p>
                                    </a>
                                </li>

                            </ul>
                        </li>


                        <li class="nav-item">

                            <a href="#" class="nav-link nav-">
                                <i class="far fa-edit"></i>
                                <p>
                                    Direcciones
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">



                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.listado.direcciones') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Direcciones</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.listado.direcciones.sinzona') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dirección sin Zona</p>
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a href="{{ route('index.callcenter.listado.direcciones.restaurante') }}" target="frameprincipal" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Direcciones Restaurante</p>
                                    </a>
                                </li>


                            </ul>
                        </li>
                    @endcan






            <!-- fin del acordeon -->
            </ul>
        </nav>




    </div>
</aside>






