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
            <h1>Restaurante: {{ $nombre }}</h1>


        </div>

        <div class="row">
            <button type="button" style="margin-left: 30px; margin-top: 15px" onclick="modalNuevo()" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i>
                Nuevo Registro
            </button>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Lista de Premios</h3>
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

<!-- modal agregar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Registro</h4>
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
                                    <label>Descripción del Premio</label>
                                    <input type="text" maxlength="150" autocomplete="off" class="form-control" id="nombre-nuevo" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <label>Costo (Puntos)</label>
                                    <input type="number" autocomplete="off" class="form-control" id="puntos-nuevo">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="guardarRegistro()">Guardar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Registro</h4>
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
                                    <label>Descripción del Premio</label>
                                    <input type="hidden" id="id-editar">

                                    <input type="text" maxlength="150" autocomplete="off" class="form-control" id="nombre-editar" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <label>Costo (Puntos)</label>
                                    <input type="number" autocomplete="off" class="form-control" id="puntos-editar">
                                </div>


                                <div class="form-group">
                                    <label>Disponible</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>


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
            var id = {{ $idservicio }};
            var ruta = "{{ URL::to('/admin/premios/servicio/listado/tabla/') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $idservicio }};
            var ruta = "{{ URL::to('/admin/premios/servicio/listado/tabla/') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        //nueva categoria
        function guardarRegistro(){

            var nombre = document.getElementById('nombre-nuevo').value;
            var puntos = document.getElementById('puntos-nuevo').value;

            if(nombre === '') {
                toastr.error('Descripción es requerida');
                return;
            }

            if(nombre.length > 150){
                toastr.error('Nombre máximo 150 caracteres')
                return;
            }


            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(puntos === ''){
                toastr.error('Puntos es requerido');
                return;
            }

            if(!puntos.match(reglaNumeroEntero)) {
                toastr.error('Para Puntos Se debe ingresar numeros enteros');
                return;
            }

            if(puntos <= 0){
                toastr.error('Puntos no debe ser negativo o cero');
                return;
            }


            if(puntos > 1000000){
                toastr.error('Puntos máximo 1 millón');
                return;
            }



            // id de servicio
            var idservicio = {{ $idservicio }};

            openLoading();

            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);

            axios.post('/admin/premios/servicio/registrar/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {

                        $('#modalAgregar').modal('hide');
                        toastr.success('Registrado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }


        function verInformacion(id){

            openLoading();

            document.getElementById("formulario-editar").reset();

            axios.post('/admin/premios/servicio/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {

                        $('#id-editar').val(id);


                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#puntos-editar').val(response.data.info.puntos);

                        if(response.data.info.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }

                        $('#modalEditar').modal('show');
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



        function editar() {

            var id = document.getElementById('id-editar').value;

            var nombre = document.getElementById('nombre-editar').value;
            var puntos = document.getElementById('puntos-editar').value;

            var cbActivo = document.getElementById('toggle-activo').checked;
            var toggleActivo = cbActivo ? 1 : 0;


            if(nombre === '') {
                toastr.error('Descripción es requerida');
                return;
            }

            if(nombre.length > 150){
                toastr.error('Nombre máximo 150 caracteres')
                return;
            }


            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(puntos === ''){
                toastr.error('Puntos es requerido');
                return;
            }

            if(!puntos.match(reglaNumeroEntero)) {
                toastr.error('Para Puntos Se debe ingresar numeros enteros');
                return;
            }

            if(puntos <= 0){
                toastr.error('Puntos no debe ser negativo o cero');
                return;
            }

            if(puntos > 1000000){
                toastr.error('Puntos máximo 1 millón');
                return;
            }


            let formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('puntos', puntos);
            formData.append('toggle', toggleActivo);

            openLoading();

            axios.post('/admin/premios/servicio/editar', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if(response.data.success === 1){

                        toastr.success('Actualizado');
                        recargar();
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
