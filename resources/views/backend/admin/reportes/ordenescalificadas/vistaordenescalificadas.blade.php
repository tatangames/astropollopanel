@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Reporte de Ordenes Calificadas por el Cliente</h1>
        </div>

    </div>
</section>

<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Formulario</h3>
                    </div>
                    <form>
                        <div class="card-body">


                            <div class="form-group">
                                <label>Seleccionar Restaurante</label>
                                <select class="form-control" id="select-restaurante">
                                    @foreach($restaurantes as $item)
                                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label>Fecha desde</label>
                                <input type="date" class="form-control" id="fechadesde-reporte">
                            </div>

                            <div class="form-group">
                                <label>Fecha hasta</label>
                                <input type="date" class="form-control" id="fechahasta-reporte">
                            </div>


                        </div>

                        <div class="card-footer" style="float: right;">
                            <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-3d button-rounded button-pill button-small" onclick="buscarReporte()">Buscar</button>
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

    <script>


        function buscarReporte(){

            var idservicio = document.getElementById('select-restaurante').value;
            var fechadesde = document.getElementById('fechadesde-reporte').value;
            var fechahasta = document.getElementById('fechahasta-reporte').value;


            if(idservicio === ''){
                toastr.error("Restaurante es requerido");
                return;
            }

            if(fechadesde === ''){
                toastr.error("Fecha desde es requerido");
                return;
            }

            if(fechahasta === ''){
                toastr.error("Fecha hasta desde es requerido");
                return;
            }

            window.open("{{ URL::to('admin/pdf/ordenes/calificadas') }}/" + idservicio + "/" +  fechadesde + "/" + fechahasta);
        }



    </script>



@stop
