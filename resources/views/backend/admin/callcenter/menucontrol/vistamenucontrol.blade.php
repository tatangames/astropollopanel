
<section class="content">
    <div class="row">
        <div class="col-md-3">
            <a href="" onclick="return false;" class="btn btn-primary btn-block mb-3">Categorias</a>

            <div class="card">

                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">

                        @foreach($arrayCategorias as $info)

                            <li class="nav-item">
                                <a href="#" class="nav-link" style="color: black; font-weight: bold" onclick="cambiarTablaProductos({{ $info->id }});return false;">
                                    <img alt="Imagenes" src="{{ url('storage/imagenes/'.$info->imagen) }}" width="35px" height="35px" />
                                    </i> {{ $info->nombre }}
                                </a>
                            </li>

                        @endforeach




                    </ul>
                </div>

            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dirección del Cliente</h3>
                    <div class="card-tools">

                    </div>
                </div>

                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">

                        <li class="nav-item">
                            <p style="font-weight: bold; margin: 16px; color: black !important;">Restaurante: </p> <p style="margin: 16px">{{ $infoDireccion->nombre }}</p>

                            <p style="font-weight: bold; margin: 16px; color: black !important;">Cliente: </p> <p style="margin: 16px">{{ $infoDireccion->direccion }}</p>

                            <p style="font-weight: bold; margin: 16px; color: black !important;">Dirección: </p> <p style="margin: 16px">{{ $infoDireccion->telefono }}</p>

                            <p style="font-weight: bold; margin: 16px; color: black !important;">Referencia: </p> <p style="margin: 16px">{{ $infoDireccion->punto_referencia }}</p>

                        </li>





                    </ul>
                </div>

            </div>

        </div>



        <!-- TABLAS CON DIRECCION YA ESTABLECIDA -->

        <div class="col-md-9">
            <div class="card card-primary card-outline">
                    <div class="card-body p-0">
                        <div class="card">

                            <div id="tablaCategoriaProducto">


                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <table id="tabla" class="table table-bordered table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 10%">Producto</th>
                                                                <th style="width: 10%">Descripción</th>
                                                                <th style="width: 10%">Precio</th>
                                                                <th style="width: 10%">Imagen</th>
                                                                <th style="width: 10%">Opciones</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            @foreach($arrayProductos as $dato)

                                                                <tr>

                                                                    <td>{{ $dato->nombre }}</td>
                                                                    <td>{{ $dato->descripcion }}</td>
                                                                    <td>{{ $dato->precio }}</td>
                                                                    <td>
                                                                        @if($dato->utiliza_imagen == 1)
                                                                            <center><img alt="Imagenes" src="{{ url('storage/imagenes/'.$dato->imagen) }}" width="75px" height="75px" /></center>
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        <button type="button" class="btn btn-success btn-xs" onclick="verModalAgregar({{ $dato->id }})">
                                                                            <i class="fa fa-plus" title="Agregar"></i>&nbsp; Agregar
                                                                        </button>
                                                                    </td>

                                                                </tr>

                                                            @endforeach


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>





                            </div>
                        </div>
                    </div>
            </div>
        </div>






    </div>

</section>


<script>
    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pagingType": "full_numbers",
            "lengthMenu": [[150, -1], [150, "Todo"]],
            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });


</script>



