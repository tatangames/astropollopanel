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
            <h1>Listado de Direcciones</h1>
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
                                <label>Zona según Restaurante:</label>
                                <select class="form-control" id="select-zona">

                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nombre del Cliente</label>
                                <input type="hidden" id="id-editar">
                                <input type="text" maxlength="100" autocomplete="off" class="form-control" id="nombre-editar" placeholder="Nombre">
                            </div>

                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" maxlength="400" autocomplete="off" class="form-control" id="direccion-editar" placeholder="Dirección">
                            </div>


                            <div class="form-group">
                                <label>Referencia (Opcional)</label>
                                <input type="text" maxlength="400" autocomplete="off" class="form-control" id="referencia-editar" placeholder="Referencia">
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" maxlength="10" autocomplete="off" class="form-control" id="telefono-editar" placeholder="teléfono">
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


<div class="modal fade" id="modalEditarRestaurante" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Dirección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar-restaurante">
                    <div class="card-body">
                        <div class="col-md-12">


                            <div class="form-group">
                                <label>Cliente</label>
                                <input type="hidden" id="id-editar-restaurante">
                                <input type="text" maxlength="100" disabled autocomplete="off" class="form-control" id="nombre-restaurante" placeholder="Nombre">
                            </div>

                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" maxlength="400" disabled autocomplete="off" class="form-control" id="direccion-restaurante" placeholder="Dirección">
                            </div>


                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text" maxlength="400" disabled autocomplete="off" class="form-control" id="referencia-restaurante" placeholder="Referencia">
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" maxlength="10" disabled autocomplete="off" class="form-control" id="telefono-restaurante" placeholder="teléfono">
                            </div>


                            <div class="form-group">
                                <p style="font-weight: bold">Al Cambiar de Restaurante se Borrara la Zona Asignada y Carrito de Compras si tuviera esta dirección en Proceso de Ordenar</p>
                            </div>


                            <div class="form-group">
                                <label>Restaurante</label>
                                <select class="form-control" id="select-restaurante">
                                    @foreach($restaurantes as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="actualizarRestaurante()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/callcenter/listado/direcciones/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function informacionEditar(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/callcenter/info/direccion/editar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#direccion-editar').val(response.data.info.direccion);
                        $('#referencia-editar').val(response.data.info.punto_referencia);
                        $('#telefono-editar').val(response.data.info.telefono);

                        document.getElementById("select-zona").options.length = 0;

                        $.each(response.data.zonas, function( key, val ){
                            if(response.data.info.id_zonas == val.id){
                                $('#select-zona').append('<option value="' +val.id_zonas +'" selected="selected">'+val.nombrezona+'</option>');
                            }else{
                                $('#select-zona').append('<option value="' +val.id_zonas +'">'+val.nombrezona+'</option>');
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

            var idzona = document.getElementById('select-zona').value;
            var nombre = document.getElementById('nombre-editar').value;
            var direccion = document.getElementById('direccion-editar').value;
            var referencia = document.getElementById('referencia-editar').value;
            var telefono = document.getElementById('telefono-editar').value;


            if(idzona === '') {
                toastr.error('Zona es requerido');
                return;
            }

            if(nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastr.error('Nombre máximo 100 caracteres');
                return;
            }


            if(direccion === '') {
                toastr.error('Dirección es requerido');
                return;
            }

            if(direccion.length > 400){
                toastr.error('Dirección máximo 400 caracteres');
                return;
            }



            if(referencia.length > 400){
                toastr.error('Referencia máximo 400 caracteres');
                return;
            }




            if(telefono === '') {
                toastr.error('Teléfono es requerido');
                return;
            }

            if(telefono.length > 10){
                toastr.error('Teléfono máximo 10 caracteres');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('idzona', idzona);
            formData.append('nombre', nombre);
            formData.append('direccion', direccion);
            formData.append('referencia', referencia);
            formData.append('telefono', telefono);

            axios.post('/admin/callcenter/editar/direccion', formData, {
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





        function informacionCambiar(id){


            // UTILIZO MISMA URL SOLO PARA OBTENER DIRECCION DEL CLIENTE

            document.getElementById("formulario-editar-restaurante").reset();
            openLoading();

            axios.post('/admin/callcenter/info/direccion/editar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditarRestaurante').modal('show');
                        $('#id-editar-restaurante').val(id);
                        $('#nombre-restaurante').val(response.data.info.nombre);
                        $('#direccion-restaurante').val(response.data.info.direccion);
                        $('#referencia-restaurante').val(response.data.info.punto_referencia);
                        $('#telefono-restaurante').val(response.data.info.telefono);


                    }else{
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }



        function actualizarRestaurante(){


            var id = document.getElementById('id-editar-restaurante').value;

            var idservicio = document.getElementById('select-restaurante').value;

            if(idservicio === '') {
                toastr.error('Restaurante es requerido');
                return;
            }


            openLoading();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('idservicio', idservicio);

            axios.post('/admin/callcenter/editar/direccion/cambiarrestaurante', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditarRestaurante').modal('hide');
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




    </script>


@endsection
