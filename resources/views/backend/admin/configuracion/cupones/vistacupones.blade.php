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
                <h1>Lista de Cupones</h1>
            </div>
            <div style="margin-top:15px; margin-left:15px">

                <button type="button" onclick="modalNuevo()" class="btn btn-success btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Cupón
                </button>

            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Listado de Cupones</h3>
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

<!-- modal agregar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Cupón</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Tipo Cupón:</label>
                                        <select class="form-control" id="select-tipocupon">
                                            @foreach($listaCupones as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre del Cupón</label>
                                        <input type="text" maxlength="50" autocomplete="off" class="form-control" id="nombre-nuevo" placeholder="Nombre del Cupón">
                                    </div>

                                    <div class="form-group">
                                        <label>Límite de usos</label>
                                        <input type="number" class="form-control" id="limite-usos" value="0">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCupon()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal editar servicio-->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>Nombre del Cupón</label>
                                <input type="hidden" id="id-editar">
                                <input type="text" maxlength="50" autocomplete="off" class="form-control" id="nombre-editar" placeholder="Nombre del Cupón">
                            </div>

                            <div class="form-group">
                                <label>Límite de usos</label>
                                <input type="number" class="form-control" id="limite-usos-editar" value="0">
                            </div>

                            <div class="form-group">
                                <label>Disponibilidad</label><br>
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
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarCupon()">Guardar</button>
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
            var ruta = "{{ URL::to('admin/cupones/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });

    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/cupones/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function guardarCupon(){
            Swal.fire({
                title: 'Guardar Cupón?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevoRegistro();
                }
            })
        }

        //nuevo servicio
        function nuevoRegistro(){

            var tipocupon = document.getElementById('select-tipocupon').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var limite = document.getElementById('limite-usos').value;


            if (tipocupon === '') {
                toastr.error("Tipo de Cupón es requerido");
                return;
            }

            if (nombre === '') {
                toastr.error("Nombre de Cupón es requerido");
                return;
            }

            if(nombre.length > 50){
                toastr.error("50 caracteres máximo para nombre");
                return;
            }


            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(limite === ''){
                toastr.error("Límite de usos es requerido");
                return;
            }

            // validacion

            if(!limite.match(reglaNumeroEntero)) {
                toastr.error('Límite de usos es invalido');
                return;
            }
            if(limite <= 0){
                toastr.error('Límite de usos no puede ser cero o negativo')
                return;
            }

            if(limite > 1000000){
                toastr.error('Límite de usos no debe superar 1 millon')
                return;
            }

            openLoading();


            var formData = new FormData();
            formData.append('tipocupon', tipocupon);
            formData.append('nombre', nombre);
            formData.append('limite', limite);

            axios.post('/admin/cupones/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        // CUPON YA REGISTRADO

                        Swal.fire({
                            title: 'Cupón Repetido',
                            text: "El nombre del cupón ya se encuentra registrado",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    if(response.data.success === 2){

                        $('#modalAgregar').modal('show');
                        toastr.success('Cupón registrado');
                        recargar();

                    }else{
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }


        // vista editar servicio
        function informacionCupon(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/cupones/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        $('#nombre-editar').val(response.data.cupones.texto_cupon);
                        $('#limite-usos-editar').val(response.data.cupones.uso_limite);

                        if(response.data.cupones.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }

                    }else{
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }

        // abrir modales de opciones de servicio
        function abrirModalOpciones(id){

            document.getElementById("formulario-opciones").reset();

            $('#id-opciones').val(id);
            $('#modalOpcion').modal('show');
        }


        // editar servicio
        function editarCupon(){

            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var limite = document.getElementById('limite-usos-editar').value;

            var cbactivo = document.getElementById('toggle-activo').checked;
            var toggleActivo = cbactivo ? 1 : 0;


            if (nombre === '') {
                toastr.error("Nombre de Cupón es requerido");
                return;
            }

            if(nombre.length > 50){
                toastr.error("50 caracteres máximo para nombre");
                return;
            }


            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(limite === ''){
                toastr.error("Límite de usos es requerido");
                return;
            }

            // validacion

            if(!limite.match(reglaNumeroEntero)) {
                toastr.error('Límite de usos es invalido');
                return;
            }

            if(limite <= 0){
                toastr.error('Límite de usos no puede ser cero o negativo')
                return;
            }

            if(limite > 1000000){
                toastr.error('Límite de usos no debe superar 1 millon')
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('limite', limite);
            formData.append('activo', toggleActivo);


            axios.post('/admin/cupones/editar', formData, {
            })
                .then((response) => {
                    closeLoading();


                    if(response.data.success === 1){
                        // CUPON YA REGISTRADO

                        Swal.fire({
                            title: 'Cupón Repetido',
                            text: "El nombre del cupón ya se encuentra registrado",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }


                    else if (response.data.success === 2) {
                        toastr.success('Cupón actualizado');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error de servidor');
                });
        }






    </script>

@endsection
