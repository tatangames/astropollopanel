<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>

                                <th style="width: 8%">Nombre</th>
                                <th style="width: 8%">Cantidad</th>
                                <th style="width: 8%">Precio</th>
                                <th style="width: 8%">Total</th>
                                <th style="width: 8%">Nota de Producto</th>
                                <th style="width: 8%">Imagen</th>


                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)

                                <tr>
                                    <td>{{ $dato->nombrepro }}</td>
                                    <td>{{ $dato->cantidad }}</td>
                                    <td>{{ $dato->precio }}</td>
                                    <td>{{ $dato->multiplicado }}</td>
                                    <td>{{ $dato->nota }}</td>
                                    <td>
                                        @if($dato->usaimagen == 1)
                                            <center><img alt="Imagenes" src="{{ url('storage/imagenes/'.$dato->imagen) }}" width="75px" height="75px" /></center>
                                        @endif
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