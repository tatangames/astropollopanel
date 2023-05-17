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
            <h1>Direcciones de Cliente</h1>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Direcciones</h3>
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
            var ruta = "{{ URL::to('admin/clientes/direcciones/listado/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>



        function verMapaRegistro(id){

            window.location.href="{{ url('/admin/clientes/direcciones/mapa/registrado') }}/"+id;

        }


        function verMapaReal(id){

            openLoading();

            var formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/clientes/tiene/gps/coordenadas', formData, {
            })
                .then((response) => {
                    closeLoading();


                    if (response.data.success === 1) {

                        Swal.fire({
                            title: 'Sin GPS',
                            text: "El cliente no tiene coordenadas registradas",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        });

                    }
                    else if (response.data.success === 2) {

                        window.location.href="{{ url('/admin/clientes/direcciones/mapa/real') }}/"+id;



                    }else{
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al buscar');
                });



        }



    </script>


@endsection
