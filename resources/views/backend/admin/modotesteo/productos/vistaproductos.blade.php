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
        <div class="row" style="margin-top: 15px">
            <button type="button" style="margin-left: 10px" onclick="modalNuevo()" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i>
                Nuevo Producto
            </button>
        </div>


        <div class="form-group" style="margin-top: 15px">
            <label>Habilitar Prueba de Primera Compra</label><br>
            <label class="switch" style="margin-top:10px">
                <input type="checkbox" id="toggle-testeo" onchange="toggleCambio()">
                <div class="slider round">
                    <span class="on">Si</span>
                    <span class="off">No</span>
                </div>
            </label>
        </div>




    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Lista de Productos para Pruebas</h3>
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
                <h4 class="modal-title">Nuevo Producto</h4>
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
                                    <label>Producto:</label>
                                    <select class="form-control" id="select-producto">
                                        @foreach($arrayPro as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>




                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="nuevo()">Guardar</button>
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
            var id = {{ $idservicio }};
            var ruta = "{{ URL::to('/admin/modoprueba/listado/productos/tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);


            var valor = {{ $infoServicio->modo_prueba }};

            cambiarToggle(valor);



        });
    </script>

    <script>

        function cambiarToggle(valor){


            if(valor === 0){
                $("#toggle-testeo").prop("checked", false);
            }else{
                $("#toggle-testeo").prop("checked", true);
            }
        }


        function toggleCambio(){

            var cbActivo = document.getElementById('toggle-testeo').checked;
            var toggleActivo = cbActivo ? 1 : 0;

            var idservicio = {{ $idservicio }};

            openLoading();

            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('valor', toggleActivo);


            axios.post('/admin/modoprueba/modificar/toggle', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al modificar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al modificar');
                    closeLoading();
                });


        }








        function recargar(){
            var id = {{ $idservicio }};
            var ruta = "{{ URL::to('/admin/modoprueba/listado/productos/tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }


        // borra la fila del producto de modo prueba
        function infoBorrar(id){

            Swal.fire({
                title: 'Ocultar Fila',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ocultar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    peticionBorrar(id);
                }
            })
        }


        function peticionBorrar(id){

            openLoading();

            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/modoprueba/listado/productos/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Ocultado correctamente');
                        recargar();
                    }
                    else {
                        toastr.error('Error al ocultar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al ocultar');
                    closeLoading();
                });

        }






        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        //nuevo producto de modo prueba
        function nuevo(){

            // id del servicio
            var idservicio = {{ $idservicio }};

            var idproducto = document.getElementById('select-producto').value;

            if(idproducto === ''){
                toastr.error('Producto es requerido');
                return;
            }

            openLoading();

            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('idproducto', idproducto);


            axios.post('/admin/modoprueba/listado/productos/nuevo', formData, {
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









    </script>


@endsection
