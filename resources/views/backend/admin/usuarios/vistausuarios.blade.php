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
                                    <input type="text" maxlength="20" disabled class="form-control" id="usuario-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" maxlength="16" disabled class="form-control" id="password-nuevo">
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
                <h4 class="modal-title">Editar Zona Servicio</h4>
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
                                    <label>Fecha ingreso</label>
                                    <input type="text" disabled class="form-control" id="fecha-editar">
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

            var idservicio = document.getElementById('select-servicios').value;




            if(selectzona === ''){
                toastr.error('Zona es requerida');
                return;
            }

            if(selectservicio === ''){
                toastr.error('Servicio es requerida');
                return;
            }

            openLoading();

            let formData = new FormData();
            formData.append('zonaservicio', selectzona);
            formData.append('servicio', selectservicio);

            axios.post('/admin/zonaservicios/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Error al Guardar',
                            text: "Esta Zona ya tiene un Servicio Asignado",
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

                    } else if (response.data.success === 2) {
                        $('#modalAgregar').modal('hide');
                        toastr.success('Zona servicio agregado');
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


        //
        function borrarRegistro(id){

            Swal.fire({
                title: 'Borrar Registro',
                text: "Esto elimina el Servicio asignado a la Zona",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Borrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrar(id);
                }
            })

        }

        // editar tipo servicio zona
        function borrar(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/zonaservicios/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Registro borrado');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error de servidor');
                });
        }

    </script>

@endsection
