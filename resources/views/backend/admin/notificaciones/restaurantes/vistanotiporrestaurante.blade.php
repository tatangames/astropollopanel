@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">

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

<section class="content">
    <div class="container-fluid" style="margin-left: 15px; margin-top: 50px">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-green">
                    <div class="card-header">
                        <h3 class="card-title">Envío de Notificaciones por Restaurante</h3>
                    </div>
                    <form>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Restaurante</label>
                                <select class="form-control" id="select-restaurante">
                                    @foreach($restaurantes as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label>Título (máximo 100 caracteres)</label>
                                <input type="text" maxlength="100" autocomplete="off" class="form-control" id="titulo" placeholder="Título">
                            </div>

                            <div class="form-group">
                                <label>Mensaje (máximo 125 caracteres)</label>
                                <input type="text" maxlength="125" autocomplete="off" class="form-control" id="mensaje" placeholder="Mensaje">
                            </div>

                        </div>

                        <div class="card-footer" style="float: right;">
                            <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-3d button-rounded button-pill button-small" onclick="preguntaEnviar()">Enviar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>





@extends('backend.menus.footerjs')
@section('archivos-js')


    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            window.seguroEnviar = true;

        });
    </script>

    <script>



        function preguntaEnviar(){

            Swal.fire({
                title: 'Enviar Notificaciones',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarNotifiacion();
                }
            })
        }

        function enviarNotifiacion(){

            var idservicio = document.getElementById('select-restaurante').value;

            var titulo = document.getElementById('titulo').value;
            var mensaje = document.getElementById('mensaje').value;


            if(idservicio === '') {
                toastr.error('Restaurante es requerido');
                return;
            }


            if(titulo === '') {
                toastr.error('Título es requerido');
                return;
            }

            if(titulo.length > 100){
                toastr.error('Título máximo 100 caracteres');
                return;
            }


            if(mensaje === '') {
                toastr.error('Mensaje es requerido');
                return;
            }

            if(mensaje.length > 125){
                toastr.error('Mensaje máximo 125 caracteres');
                return;
            }

            if(seguroEnviar){
                seguroEnviar = false;

                openLoading();

                var formData = new FormData();
                formData.append('idservicio', idservicio);
                formData.append('titulo', titulo);
                formData.append('mensaje', mensaje);

                axios.post('/admin/notificaciones/enviar/porservicio', formData, {
                })
                    .then((response) => {
                        closeLoading();

                        if (response.data.success === 1) {

                            // NOTIFICACION ENVIADA CORRECTAMENTE

                            $('#titulo').val("");
                            $('#mensaje').val("");

                            alertaEnviados();
                        }
                        else {
                            toastr.error('Error al enviar');
                        }
                    })
                    .catch((error) => {
                        seguroEnviar = true;
                        toastr.error('Error al enviar');
                        closeLoading();
                    });
            }
        }


        function alertaEnviados(){

            Swal.fire({
                title: 'Enviado Correctamente',
                text: "",
                icon: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload()
                }
            })
        }




    </script>


@endsection
