@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
    #card-header-color {
        background-color: #007bff !important;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <h1>Listado de Direcciones para Restaurante</h1>
        </div>
        <button type="button" style="margin-top: 15px;" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nueva Dirección
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Listado</h3>
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
    </div>
</section>


<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Dirección</h4>
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
                                    <label>Restaurante:</label>
                                    <select class="form-control" id="select-servicio">
                                        @foreach($restaurantes as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" autocomplete="off" maxlength="600" class="form-control" id="direccion-nuevo">
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





<!-- modal editar-->
<div class="modal fade" id="modalEditar" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Dirección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="col-md-12">


                            <div class="form-group">
                                <label>Restaurante:</label>
                                <select class="form-control" id="select-servicio-editar">

                                </select>
                            </div>

                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="hidden" id="id-editar">
                                <input type="text" autocomplete="off" maxlength="600" class="form-control" id="direccion-editar">
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/restaurante/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/restaurante/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }


        function nuevo(){

            var idservicio = document.getElementById('select-servicio').value;
            var direccion = document.getElementById('direccion-nuevo').value;


            if(idservicio === ''){
                toastr.error('Restaurante es requerida');
                return;
            }

            if(direccion === ''){
                toastr.error('Dirección es requerida');
                return;
            }

            if(direccion.length > 600){
                toastr.error('Dirección máximo 600 caracteres');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('direccion', direccion);


            axios.post('/admin/callcenter/restaurante/direccion/nueva', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        $('#modalAgregar').modal('hide');
                        toastr.success('Dirección registrado');
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



        function informacionEditar(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/callcenter/restaurante/direccion/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#direccion-editar').val(response.data.info.direccion);

                        document.getElementById("select-servicio-editar").options.length = 0;

                        $.each(response.data.servicios, function( key, val ){
                            if(response.data.info.id_servicio == val.id){
                                $('#select-servicio-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-servicio-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                    }else{
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function editar(){

            var id = document.getElementById('id-editar').value;

            var idservicio = document.getElementById('select-servicio-editar').value;
            var direccion = document.getElementById('direccion-editar').value;

            if(idservicio === ''){
                toastr.error('Restaurante es requerida');
                return;
            }

            if(direccion === ''){
                toastr.error('Dirección es requerida');
                return;
            }

            if(direccion.length > 600){
                toastr.error('Dirección máximo 600 caracteres');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('idservicio', idservicio);
            formData.append('direccion', direccion);


            axios.post('/admin/callcenter/restaurante/direccion/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al Editar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Editar');
                    closeLoading();
                });
        }




        function modalBorrarDireccion(id){

            Swal.fire({
                title: 'Borrar Dirección',
                text: "",
                icon: 'success',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarDireccion(id);
                }
            })
        }

        function borrarDireccion(id){

            openLoading();

            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/callcenter/restaurante/direccion/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });

        }



    </script>


@endsection
