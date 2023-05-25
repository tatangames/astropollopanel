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
            <h1>Listado de Direcciones sin Zona Asignada</h1>
        </div>
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
                                <label>Cliente</label>
                                <input type="hidden" id="id-editar">
                                <input type="text" disabled autocomplete="off" class="form-control" id="cliente">
                            </div>


                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="direccion">
                            </div>

                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text" disabled autocomplete="off" class="form-control" id="referencia">
                            </div>


                            <div class="form-group" style="margin-top: 15px">
                                <label style="font-weight: bold">Zona segun Restaurante:</label>
                                <select class="form-control" id="select-zonas">
                                </select>
                            </div>



                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editar()">Actualizar</button>
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
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/sinzona/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/sinzona/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function informacionEditar(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/callcenter/listado/direccion/sinzona/info',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        $('#cliente').val(response.data.info.nombre);
                        $('#direccion').val(response.data.info.direccion);
                        $('#referencia').val(response.data.info.punto_referencia);

                        document.getElementById("select-zonas").options.length = 0;

                        // ESTOS NO TIENEN ZONAS, ASI QUE SOLO LLENAR

                        $.each(response.data.zonas, function( key, val ){
                            $('#select-zonas').append('<option value="' +val.id_zonas +'" selected="selected">'+val.nombrezona+'</option>');
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
            var idzona = document.getElementById('select-zonas').value;

            if(idzona === ''){
                toastr.error('Zona es requerida');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('idzona', idzona);

            axios.post('/admin/callcenter/listado/direccion/sinzona/editar', formData, {
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
