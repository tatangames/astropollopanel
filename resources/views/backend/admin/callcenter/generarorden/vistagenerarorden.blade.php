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
                <h1>Generar Orden</h1>
            </div>
        </div>
    </div>
</section>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form>
                <div class="input-group">

                    <input type="text" style="max-width: 350px" class="form-control form-control-lg noEnterSubmit" placeholder="Número Telefónico">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-lg btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<hr>

<div id="tablaDatatable">

</div>



<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Motorista</h4>
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
                                    <label>Nombre del Motorista</label>
                                    <input type="text" autocomplete="off" maxlength="100" class="form-control" id="nombre-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Vehículo</label>
                                    <input type="text" autocomplete="off" maxlength="50" class="form-control" id="vehiculo-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Placa</label>
                                    <input type="text" autocomplete="off" maxlength="50" class="form-control" id="placa-nuevo">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Foto del Motorista</label>
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
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
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


            $('.noEnterSubmit').keypress(function(e){
                if ( e.which == 13 ) return false;
                //or...
                if ( e.which == 13 ) e.preventDefault();
            });





        });

    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/motoristas/usuario/tabla') }}";
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
            var vehiculo = document.getElementById('vehiculo-nuevo').value;
            var placa = document.getElementById('placa-nuevo').value;
            var imagen = document.getElementById('imagen-nuevo');


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

            if(vehiculo === ''){
                toastr.error('Vehiculo es requerida');
                return;
            }

            if(placa === ''){
                toastr.error('Placa es requerida');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                toastr.error('Foto es requerida');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('usuario', usuario);
            formData.append('password', password);
            formData.append('nombre', nombre);
            formData.append('vehiculo', vehiculo);
            formData.append('placa', placa);
            formData.append('imagen', imagen.files[0]);



            axios.post('/admin/motoristas/usuario/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Error al Guardar',
                            text: "El usuario ya esta registrado para un Restaurante",
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

                    else if (response.data.success === 2) {
                        $('#modalAgregar').modal('hide');
                        toastr.success('Motorista registrado');
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


        function informacion(id){

            openLoading();

            axios.post('/admin/motoristas/usuario/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {

                        $('#id-editar').val(id);


                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#usuario-editar').val(response.data.info.usuario);
                        $('#vehiculo-editar').val(response.data.info.vehiculo);
                        $('#placa-editar').val(response.data.info.placa);


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
            var password = document.getElementById('password-editar').value;

            var usuario = document.getElementById('usuario-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var vehiculo = document.getElementById('vehiculo-editar').value;
            var placa = document.getElementById('placa-editar').value;

            var cbActivo = document.getElementById('toggle-activo').checked;
            var toggleActivo = cbActivo ? 1 : 0;

            var imagen = document.getElementById('imagen-editar');



            if (usuario === '') {
                toastr.error("Usuario es requerido");
                return;
            }


            if (nombre === '') {
                toastr.error("Nombre es requerido");
                return;
            }

            if (vehiculo === '') {
                toastr.error("Vehiculo es requerido");
                return;
            }

            if (placa === '') {
                toastr.error("Placa es requerido");
                return;
            }

            if (password.length > 16) {
                toastr.error("Contraseña máximo 16 caracteres");
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }


            let formData = new FormData();
            formData.append('id', id);
            formData.append('password', password);
            formData.append('usuario', usuario);
            formData.append('nombre', nombre);
            formData.append('vehiculo', vehiculo);
            formData.append('placa', placa);
            formData.append('activo', toggleActivo);
            formData.append('imagen', imagen.files[0]);


            openLoading();

            axios.post('/admin/motoristas/usuario/editar', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'No Guardado',
                            text: "El Usuario ya se encuentra registrado",
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

                    else if (response.data.success === 2) {
                        toastr.success('Registro actualizado');
                        $('#modalEditar').modal('hide');

                        recargar();
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
