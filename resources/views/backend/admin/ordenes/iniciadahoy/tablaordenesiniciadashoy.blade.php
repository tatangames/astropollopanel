<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 8%"># de Orden</th>
                                <th style="width: 10%">Fecha de Orden</th>
                                <th style="width: 10%">Total</th>
                                <th style="width: 10%">Restaurante</th>
                                <th style="width: 10%">Cliente</th>
                                <th style="width: 10%">Dirección</th>
                                <th style="width: 10%">Cupón</th>
                                <th style="width: 10%">Estimada Entrega</th>
                                <th style="width: 8%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($ordenes as $dato)

                                <tr>
                                    <td>{{ $dato->id }}</td>
                                    <td>{{ $dato->fecha_orden }}</td>
                                    <td>{{ $dato->total_orden }}</td>
                                    <td>{{ $dato->restaurante }}</td>
                                    <td>{{ $dato->cliente }}</td>
                                    <td>{{ $dato->direccion }}</td>
                                    <td>
                                        @if($dato->sicupon == 0)
                                            <span>No</span>
                                        @else
                                            <span class="badge bg-success">Si</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->horaEstimadaEntrega }}</td>

                                    <td>

                                        <button type="button" class="btn btn-success btn-xs" onclick="verCliente({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Cliente"></i>&nbsp; Cliente
                                        </button>

                                        <br><br>
                                        <button type="button" class="btn btn-success btn-xs" onclick="verProceso({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Proceso"></i>&nbsp; Proceso
                                        </button>

                                        <br><br>
                                        <button type="button" class="btn btn-success btn-xs" onclick="verProductos({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Productos"></i>&nbsp; Productos
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
            "lengthMenu": [[10, 25, 50, 100, 150, -1], [10, 25, 50, 100, 150, "Todo"]],
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
