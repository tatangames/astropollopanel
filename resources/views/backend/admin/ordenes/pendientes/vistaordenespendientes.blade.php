@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Listado de Ordenes Pendientes de Iniciar</h1>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
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


<div class="modal fade" id="modalCliente">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Información de Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-cliente">
                    <div class="card-body">
                        <div class="col-md-12">





                            <div class="form-group">
                                <label>Nombre del Cliente</label>
                                <input type="hidden" id="id-cliente">
                                <input type="text" disabled autocomplete="off" class="form-control" id="nombre-cliente">
                            </div>


                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="direccion-cliente">
                            </div>

                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="referencia-cliente">
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="telefono-cliente">
                            </div>

                            <div class="form-group">
                                <label>Versión de App</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="version-cliente">
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Mapa de Entrega</label>
                                <br>
                                <button type="button" class="btn btn-primary" onclick="mapaRegistro()">Mapa</button>
                            </div>





                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
            var ruta = "{{ URL::to('admin/ordenes/pendientes/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/ordenes/pendientes/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function verCliente(id){


            document.getElementById("formulario-cliente").reset();

            openLoading();

            axios.post('/admin/ordenes/pendientes/infocliente', {
                'id': id
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {

                        // id de la orden
                        $('#id-cliente').val(id);

                        $('#nombre-cliente').val(response.data.cliente.nombre);
                        $('#direccion-cliente').val(response.data.cliente.direccion);
                        $('#referencia-cliente').val(response.data.cliente.referencia);
                        $('#telefono-cliente').val(response.data.cliente.telefono);
                        $('#version-cliente').val(response.data.cliente.appversion);


                        $('#modalCliente').modal('show');
                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }


        function mapaRegistro(){

            // id de orden
            var id = document.getElementById('id-cliente').value;

            window.location.href="{{ url('/admin/ordenes/pendientes/mapa/') }}/"+id;
        }



    </script>


@endsection
