@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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
            <h1>Banner</h1>

            <button type="button" style="margin-left: 30px" onclick="modalNuevo()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                Nuevo Banner
            </button>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Lista de Banner</h3>
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
                <h4 class="modal-title">Nuevo Banner</h4>
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
                                    <label>Descripción del Banner</label>
                                    <input type="text" autocomplete="off" maxlength="200" class="form-control" id="nombre-nuevo">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Redirecciona a Producto?</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-redireccion">
                                        <div class="slider round">
                                            <span class="on">Sí</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>En caso que no redireccione a ningún Producto, puede dejar por defecto (Seleccionar opción)</label>
                                </div>

                                <div class="form-group">
                                    <label>Producto:</label>
                                    <select class="form-control" id="select-producto-nuevo">
                                        <option value=""> Seleccionar opción</option>
                                        @foreach($arrayProductos as $dd)
                                            <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                        @endforeach
                                    </select>
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

                                <p>Si no utiliza Horario este Slider, establecer uno por Defecto</p>

                                <div class="form-group">
                                    <label>Horario abre</label>
                                    <input type="time" class="form-control" id="hora-abre">
                                </div>

                                <div class="form-group">
                                    <label>Horario cierra</label>
                                    <input type="time" class="form-control" id="hora-cierra">
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
                <h4 class="modal-title">Editar Banner</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group" style="margin-left:0px">
                                <label>Slider Disponible</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-activo-editar">
                                    <div class="slider round">
                                        <span class="on">Sí</span>
                                        <span class="off">No</span>
                                    </div>
                                </label>
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/slider/listado/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);


            $('#select-producto-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });


            $('#select-producto-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

        });
    </script>

    <script>

        function recargar(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/slider/listado/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();
            $("#select-producto-nuevo").val('').trigger('change');

            $('#modalAgregar').modal('show');
        }

        //nuevo servicio
        function nuevo(){

            var idservicio = {{ $id }};
            var imagen = document.getElementById('imagen-nuevo');
            var producto = document.getElementById('select-producto-nuevo').value;
            var nombre = document.getElementById('nombre-nuevo').value;

            var cbcategoria = document.getElementById('toggle-redireccion').checked;
            var cbhorario = document.getElementById('toggle-horario').checked;
            var horaabre = document.getElementById('hora-abre').value;
            var horacierra = document.getElementById('hora-cierra').value;


            var toggleRedireccion = cbcategoria ? 1 : 0;
            var toggleHorario = cbhorario ? 1 : 0;

            if(nombre.length > 200){
                toastr.error('Nombre debe tener 200 caracteres máximo');
                return;
            }


            if(toggleRedireccion === 1){
                if(producto === '' || producto == null){
                    toastr.error('Seleccionar Producto');
                    return;
                }
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

            if(horaabre === ''){
                toastr.error('horario Abre es requerido');
                return;
            }

            if(horacierra === ''){
                toastr.error('horario Cierra es requerido');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('imagen', imagen.files[0]);
            formData.append('producto', producto);
            formData.append('nombre', nombre);
            formData.append('toggledireccion', toggleRedireccion);
            formData.append('togglehorario', toggleHorario);
            formData.append('horaabre', horaabre);
            formData.append('horacierra', horacierra);

            axios.post('/admin/slider/nuevo', formData, {
            })
                .then((response) => {

                    console.log(response);
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

            axios.post('/admin/slider/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(id);

                        if(response.data.slider.activo === 0){
                            $("#toggle-activo-editar").prop("checked", false);
                        }else{
                            $("#toggle-activo-editar").prop("checked", true);
                        }

                    }else{
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error de servidor');
                    closeLoading();
                });
        }

        function editar(){

            var idslider = document.getElementById('id-editar').value;
            var cbactivo = document.getElementById('toggle-activo-editar').checked;

            var toggleActivo = cbactivo ? 1 : 0;

            openLoading();
            var formData = new FormData();
            formData.append('idslider', idslider);
            formData.append('toggleactivo', toggleActivo);

            axios.post('/admin/slider/editar', formData, {
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

        function modalBorrar(id){
            Swal.fire({
                title: 'Borrar Banner?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarSlider(id);
                }
            })
        }

        function borrarSlider(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/slider/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('hide');
                        toastr.success('Borrado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al Borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al Borrar');
                    closeLoading();
                });
        }

    </script>


@endsection
