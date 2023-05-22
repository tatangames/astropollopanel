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
            <div class="col-sm-7">
                <h1>Generar Orden</h1>
            </div>

            <div class="col-md-4">

                <button type="button" style="float: right" class="btn btn-lg btn-danger">
                    <i class="fa fa-trash"> Borrar Carrito</i>
                </button>
            </div>

        </div>
    </div>
</section>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form>

                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" style="max-width: 350px" id="numero-cliente" class="form-control form-control-lg noEnterSubmit" placeholder="Número Telefónico">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-lg btn-default" onclick="buscarNumero()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<hr>


<!-- SIN DIRECCION ASIGNADA -->



<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Control</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaMenuRestaurante">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>








<!-- MODAL NUEVA DIRECCION -->
<div class="modal fade" id="modalDireccionNueva">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Dirección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-direccionnueva">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Restaurante:</label>
                                    <select class="form-control" id="select-servicios">
                                        @foreach($restaurantes as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" autocomplete="off" maxlength="100" class="form-control" id="nombre-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="direccion-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" autocomplete="off" maxlength="10" class="form-control" id="telefono-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Punto de Referencia</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="referencia-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarDireccion()">Guardar</button>
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

            var ruta = "{{ URL::to('admin/callcenter/todo/restaurante/asignado') }}";
            $('#tablaMenuRestaurante').load(ruta);

        });

    </script>

    <script>

        // BUSCAR SI CLIENTE TIENE REGISTRO CON SU NUMERO
        function buscarNumero(){

            var numero = document.getElementById('numero-cliente').value;

            if(numero === ''){
                toastr.error('Número Telefónico es requerido');
                return;
            }
            openLoading();

            let formData = new FormData();
            formData.append('numero', numero);


            axios.post('/admin/callcenter/buscar/numero', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // SI HAY DIRECCIONES, CARGAR LA TABLA

                        toastr.error('si haya array direcciones');

                    }

                    else if (response.data.success === 2) {
                       // NO SE ENCONTRO NUMERO REGISTRADO

                        document.getElementById("formulario-direccionnueva").reset();
                        $('#telefono-nuevo').val(numero);

                        $('#modalDireccionNueva').modal({backdrop: 'static', keyboard: false})

                    }  else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }



        function preguntaGuardarDireccion(){

            Swal.fire({
                title: 'Guardar Nueva Dirección?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarNuevaDireccion();
                }
            })
        }


        function guardarNuevaDireccion(){

            var restaurante = document.getElementById('select-servicios').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var direccion = document.getElementById('direccion-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var referencia = document.getElementById('referencia-nuevo').value;

            if(restaurante === ''){
                toastr.error('Restaurante es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Dirección es requerido');
                return;
            }

            if(nombre.length > 100){
                toastr.error('100 caracteres para Nombre máximo');
                return;
            }

            if(direccion === ''){
                toastr.error('Dirección es requerido');
                return;
            }

            if(direccion.length > 400){
                toastr.error('400 caracteres para Dirección máximo');
                return;
            }


            if(telefono === ''){
                toastr.error('Teléfono es requerido');
                return;
            }

            if(telefono.length > 10){
                toastr.error('10 caracteres para Teléfono máximo');
                return;
            }


            if(referencia === ''){
                toastr.error('Referencia es requerido');
                return;
            }

            if(referencia.length > 400){
                toastr.error('400 caracteres para Referencia máximo');
                return;
            }

            openLoading();

            let formData = new FormData();
            formData.append('servicio', restaurante);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('direccion', direccion);
            formData.append('referencia', referencia);

            axios.post('/admin/callcenter/guardar/nueva/direccion', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // DIRECCION GUARDADA, HOY MOSTRAR LA TABLA DE PRODUCTOS
                        toastr.success('Dirección Guardada');
                        $('#modalDireccionNueva').modal('hide');

                        cargarTablaProductos();
                    }

                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }



        function cargarTablaProductos(){



        }



        function verModalAgregar(id){



        }











    </script>

@endsection
