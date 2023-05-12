@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>

    #card-header-color {
        background-color: #673AB7 !important;
    }

</style>

<section class="content-header">
    <div class="container-fluid">
        <h1>Zona: {{ $nombre }}</h1>
        <br>
        <button type="button" onclick="preguntaBorrarPoligono()" class="btn btn-danger btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Borrar Todos los Poligonos
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" id="card-header-color">
                        <h3 class="card-title" style="color:white;">Registrar Poligonos</h3>
                    </div>
                    <form>
                        <div class="card-body">

                            <table class="table" id="matriz"  data-toggle="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Latitud</th>
                                    <th scope="col">Longitud</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                                </thead>
                                <tbody id="myTbody">

                                <tr id="0">
                                    <td><p name="fila[]" disabled id="fila0" class="form-control" style="max-width: 65px">1</td>
                                    <td><input name="latitud[]" maxlength="50" class="form-control" type="text"></td>
                                    <td><input name="longitud[]" maxlength="50" class="form-control" type="text"></td>

                                    <td><button type="button" class="btn btn-block btn-danger" id="btnBorrar" onclick="borrarFila(this)">Borrar</button></td>
                                </tr>

                                </tbody>

                            </table>

                            <br>
                            <button type="button" class="btn btn-block btn-success" id="btnAdd">Agregar Fila</button>
                            <br>
                            <div class="card-footer">
                                <button id="btnguardar" type="button"  class="btn btn-success float-right" onclick="verificar();">Guardar Registros</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        // filas de la tabla
        $(document).ready(function () {
            $("#btnAdd").on("click", function () {

                var nFilas = $('#matriz >tbody >tr').length;
                nFilas += 1;

                //agrega las filas dinamicamente

                var markup = "<tr id='"+(nFilas)+"'>"+

                    "<td>"+
                    "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                    "</td>"+

                    "<td>"+
                    "<input name='latitud[]' maxlength='50' class='form-control' type='text' value=''>"+
                    "</td>"+

                    "<td>"+
                    "<input name='longitud[]' maxlength='50' class='form-control' type='text' value=''>"+
                    "</td>"+

                    "<td>"+
                    "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                    "</td>"+

                    "</tr>";

                $("tbody").append(markup);

            });
        });

        // verificar que todos los datos a ingresar sean correctos
        function verificar(){

            // minimo se necesitara 1 registro para guardar
            var nRegistro = $('#matriz >tbody >tr').length;
            if (nRegistro <= 0){
                toastr.error('Ingresar 1 registro como mínimo');
                return;
            }

            var latitud = $("input[name='latitud[]']").map(function(){return $(this).val();}).get();
            var longitud = $("input[name='longitud[]']").map(function(){return $(this).val();}).get();

            for(var a = 0; a < latitud.length; a++){

                var datoLatitud = latitud[a];

                if(datoLatitud === ''){
                    toastr.error('Campo Requerido','En la fila "'+(a+1)+'", se debe ingresar Latitud');
                    return;
                }

                if(datoLatitud.length > 50) {
                    toastr.error('En la fila "' + (a+1) + '", los caracteres máximo son 50 para Latitud y ha ingresado: "'+datoLatitud.length+'"')
                    return;
                }
            }

            for(var b = 0; b < longitud.length; b++){

                var datoLongitud = longitud[b];

                if(datoLongitud === ''){
                    toastr.error('Campo Requerido','En la fila "'+(b+1)+'", se debe ingresar Longitud')
                    return;
                }

                if(datoLongitud.length > 50) {
                    toastr.error('En la fila "' + (b+1) + '", los caracteres máximo son 50 para Longitud y ha ingresado: "'+datoLongitud.length+'"')
                    return;
                }
            }

            Swal.fire({
                title: 'Registrar Polígonos?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarRegistro();
                }
            })
        }


        // guardado de registros del ingreso
        function guardarRegistro(){

            openLoading();
            // verificar los primeros campos select

            var id = {{ $id }};

            var latitud = $("input[name='latitud[]']").map(function(){return $(this).val();}).get();
            var longitud = $("input[name='longitud[]']").map(function(){return $(this).val();}).get();

            let formData = new FormData();

            formData.append('id', id);

            // solo con recorrer latitud es suficiente, por tener la misma cantidad de filas
            for(var a = 0; a < latitud.length; a++){
                formData.append('latitud[]', latitud[a]);
                formData.append('longitud[]', longitud[a]);
            }
            axios.post('/admin/zonas/poligono/listado-nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        // registrado correctamente
                        toastr.success('Poligonos Registrados');
                        borrarTabla();
                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }

        // borrar todas las filas e ingresar una en blanco
        function borrarTabla(){
            $("#matriz tbody tr").remove();

            var nFilas = $('#matriz >tbody >tr').length;
            nFilas += 1;

            //agrega las filas dinamicamente

            var markup = "<tr id='"+(nFilas)+"'>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='latitud[]' maxlength='50' class='form-control' type='text' value=''>"+
                "</td>"+

                "<td>"+
                "<input name='longitud[]' maxlength='50' class='form-control' type='text' value=''>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFila(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("tbody").append(markup);
        }


        function borrarFila(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFila();
        }

        // cambiar # de fila cada vez que se borre una fila
        function setearFila(){

            var table = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function abrirModalBorrar(){
            $('#modalBorrar').modal('show');
        }

        function preguntaBorrarPoligono(){

            Swal.fire({
                title: 'Borrar Polígonos?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarPoligonos();
                }
            })
        }

        function borrarPoligonos(){

            var id = {{ $id }};

            openLoading();
            let formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/zonas/poligono/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado Correctamente');
                        $('#modalBorrar').modal('hide');
                    }
                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }

    </script>

@stop
