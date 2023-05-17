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
        background-color: #673AB7 !important;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <h1>Categorías</h1>

            <button type="button" style="margin-left: 30px" onclick="modalNuevo()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                Nueva Categoría
            </button>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Lista de Categorías</h3>
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
                <h4 class="modal-title">Nuevo Categoría</h4>
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
                                    <label>Nombre de Categoria</label>
                                    <input type="text" maxlength="100" class="form-control" autocomplete="off" id="nombre-nuevo" placeholder="Nombre">
                                </div>

                                <div class="form-group" >
                                    <label>Utiliza Horario</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-horario">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <p>Si no utiliza Horario esta Categoría, establecer uno por Defecto</p>

                                <div class="form-group">
                                    <label>Horario abre</label>
                                    <input type="time" class="form-control" id="hora-abre">
                                </div>
                                <div class="form-group">
                                    <label>Horario cierra</label>
                                    <input type="time" class="form-control" id="hora-cierra">
                                </div>


                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRegistro()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Categoría</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="hidden" id="id-editar">
                                <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre">
                            </div>

                            <div class="form-group" style="margin-left:0px">
                                <label>Activo</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-activo">
                                    <div class="slider round">
                                        <span class="on">Activo</span>
                                        <span class="off">Inactivo</span>
                                    </div>
                                </label>
                            </div>


                            <div class="form-group">
                                <label>Utiliza Horario</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-horario-editar">
                                    <div class="slider round">
                                        <span class="on">Si</span>
                                        <span class="off">No</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group">
                                <label>Horario abre</label>
                                <input type="time" class="form-control" id="hora-abre-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra</label>
                                <input type="time" class="form-control" id="hora-cierra-editar">
                            </div>


                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>
        </div>
    </div>
</div>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/categorias/listado/tabla/') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/categorias/listado/tabla/') }}/"+id;
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
            var horaabre = document.getElementById('hora-abre').value;
            var horacierra = document.getElementById('hora-cierra').value;
            var cbtoggle = document.getElementById('toggle-horario').checked;
            var imagen = document.getElementById('imagen-nuevo');

            var toggleHorario = cbtoggle? 1 : 0;
            var id = {{ $id }};

            if(nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastr.error('Nombre máximo 100 caracteres');
                return;
            }


            if (horaabre === '') {
                toastr.error("Horario Abre horario es requerido");
                return;
            }

            if (horacierra === '') {
                toastr.error("Horario Cierre horario es requerido");
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                toastr.error('Imagen es requerido');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('imagen', imagen.files[0]);
            formData.append('horaabre', horaabre);
            formData.append('horacierra', horacierra);
            formData.append('toggle', toggleHorario);

            axios.post('/admin/categorias/nuevo', formData, {
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

        function informacion(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/categorias/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.categoria.nombre);

                        if(response.data.categoria.usa_horario === 0){
                            $("#toggle-horario-editar").prop("checked", false);
                        }else{
                            $("#toggle-horario-editar").prop("checked", true);
                        }

                        $('#hora-abre-editar').val(response.data.categoria.hora_abre);
                        $('#hora-cierra-editar').val(response.data.categoria.hora_cierra);


                        if(response.data.categoria.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }

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
            var nombre = document.getElementById('nombre-editar').value;
            var cbactivo = document.getElementById('toggle-activo').checked;

            var toggleActivo = cbactivo ? 1 : 0;

            var cbhorario = document.getElementById('toggle-horario-editar').checked;
            var toggleHorario = cbhorario ? 1 : 0;

            var horaabre = document.getElementById('hora-abre-editar').value;
            var horacierra = document.getElementById('hora-cierra-editar').value;


            if(nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastr.error('Nombre máximo 100 caracteres');
                return;
            }

            if(horaabre === ''){
                toastr.error('Horario abre es requerido');
            }

            if(horacierra === ''){
                toastr.error('Horario cierra es requerido');
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('cbactivo', toggleActivo);
            formData.append('cbhorario', toggleHorario);
            formData.append('horaabre', horaabre);
            formData.append('horacierra', horacierra);

            axios.post('/admin/categorias/editar', formData, {
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

        function verSubCategorias(id) {
            window.location.href="{{ url('/admin/sub/categorias/listado') }}/"+id;
        }


    </script>


@endsection
