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
            <h1>Cupones Asignados</h1>

            <button type="button" style="margin-left: 30px" onclick="modalNuevo()" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i>
                Agregar Cupón
            </button>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" id="card-header-color">
                <h3 class="card-title" style="color: white">Lista de Cupones de Descuento Dinero</h3>
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
                <h4 class="modal-title">Registrar Cupón</h4>
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
                                    <label>Nombre de Cupón de Descuento de Dinero:</label>
                                    <select class="form-control" id="select-tipocupon">
                                        @foreach($lista as $item)
                                            <option value="{{$item->id}}">{{$item->texto_cupon}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Monto a Descontar</label>
                                    <input type="text" class="form-control" autocomplete="off" id="monto-nuevo" placeholder="Monto">
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
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/cupones/servicio/descdinero/tabla/') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/cupones/servicio/descdinero/tabla/') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        //nueva categoria
        function guardarRegistro(){

            var tipocupon = document.getElementById('select-tipocupon').value;
            var monto = document.getElementById('monto-nuevo').value;

            var idservicio = {{ $id }};

            if(tipocupon === '') {
                toastr.error('Tipo de Cupón es requerido');
                return;
            }


            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(monto === '') {
                toastr.error('Monto es requerido');
                return;
            }

            if(!monto.match(reglaNumeroDosDecimal)) {
                toastr.error('Monto debe ser número decimal y 2 decimales');
                return;
            }

            if(monto <= 0){
                toastr.error('Monto no debe ser negativo o cero');
                return;
            }

            if(monto > 1000000){
                toastr.error('Monto no debe ser mayor a 1 millón');
                return;
            }



            openLoading();

            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('idcupon', tipocupon);
            formData.append('monto', monto);

            axios.post('/admin/cupones/servicio/descdinero/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    // EL MISMO CUPON YA ESTA REGISTRADO
                    if(response.data.success === 1){

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

        function informacionBorrar(id){

            Swal.fire({
                title: 'Borrar Cupón',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#e50808',
                confirmButtonText: 'Borrar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarCupon(id);
                }
            })
        }


        function borrarCupon(id){

            openLoading();

            axios.post('/admin/cupones/servicio/descdinero/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        toastr.success('Cupón borrado');
                        recargar();

                    }else{
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
