@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Usuarios para Restaurante</h1>
            </div>
            <div style="margin-top:15px;">
                <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Usuario
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="tablaDatatable">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Solo es permitido 1 usuario por cada Restaurante</label>
                                </div>

                                <div class="form-group">
                                    <label>Restaurante:</label>
                                    <select class="form-control" id="select-servicio">
                                        @foreach($servicios as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Usuario</label>
                                    <input type="text" autocomplete="off" maxlength="20" class="form-control" id="usuario-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" autocomplete="off" maxlength="16" class="form-control" id="password-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Nombre del Usuario</label>
                                    <input type="text" autocomplete="off" maxlength="100" class="form-control" id="nombre-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Contraseña</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Nueva Contraseña</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" autocomplete="off" maxlength="16" class="form-control" id="password-editar">
                                </div>



                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
            </div>
        </div>
    </div>
</div>



@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('admin/restaurantes/usuario/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });

    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/restaurantes/usuario/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        // modal nuevo
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        // agregar
        function nuevo(){

            var idservicio = document.getElementById('select-servicio').value;
            var usuario = document.getElementById('usuario-nuevo').value;
            var password = document.getElementById('password-nuevo').value;
            var nombre = document.getElementById('nombre-nuevo').value;

            if(idservicio === ''){
                toastr.error('Restaurante es requerida');
                return;
            }

            if(usuario === ''){
                toastr.error('Usuario es requerida');
                return;
            }

            if(password === ''){
                toastr.error('Contraseña es requerida');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerida');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('usuario', usuario);
            formData.append('password', password);
            formData.append('nombre', nombre);

            axios.post('/admin/restaurantes/usuario/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Error al Guardar',
                            text: "Este Restaurante ya tiene asignado 1 usuario",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    }

                    else if(response.data.success === 2){

                        Swal.fire({
                            title: 'Error al Guardar',
                            text: "El usuario ya esta registrado para un Restaurante, siempre se toma en cuenta los usuarios bloqueados",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    }

                    else if (response.data.success === 3) {
                        $('#modalAgregar').modal('hide');
                        toastr.success('Usuario registrado');
                        recargar();
                    }  else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }

        function borrarRegistro(id){

            Swal.fire({
                title: 'Bloquear Usuario',
                text: "Esto bloquea el usuario y no podra iniciar sesión o iniciar ordenes",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Bloquear'
            }).then((result) => {
                if (result.isConfirmed) {
                    bloquear(id);
                }
            })

        }

        // editar
        function bloquear(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/restaurantes/usuario/bloquear', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Registro bloqueado');
                        recargar();
                    }
                    else {
                        toastr.error('Error al bloquear');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error de servidor');
                });
        }



        function cambioPassword(id){
            document.getElementById("formulario-editar").reset();
            $('#modalEditar').modal('show');

            $('#id-editar').val(id);
        }



        function editar() {
            var id = document.getElementById('id-editar').value;
            var password = document.getElementById('password-editar').value;

            if (password === '') {
                toastr.error("Contraseña nueva es requerido");
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('password', password);

            openLoading();

            axios.post('/admin/restaurantes/usuario/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading()

                     if (response.data.success === 1) {
                        toastr.success('Contraseña actualizada');
                        $('#modalEditar').modal('hide');
                    }

                    else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }





    </script>

@endsection
