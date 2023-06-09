<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>

                                <th style="width: 10%">Fecha de Registro</th>
                                <th style="width: 10%">Usuario</th>
                                <th style="width: 10%">Correo</th>
                                <th style="width: 10%">App Registro</th>
                                <th style="width: 10%">Usuario Activo</th>

                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)

                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->usuario }}</td>
                                    <td>{{ $dato->correo }}</td>
                                    <td>{{ $dato->appregistro }}</td>

                                    <td>
                                        @if($dato->activo == 0)
                                            <span class="badge bg-danger">Desactivado</span>
                                        @else
                                            <span class="badge bg-success">Activado</span>
                                        @endif
                                    </td>



                                    <td>

                                        <button type="button" class="btn btn-primary btn-xs" onclick="verInformacion({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <br><br>
                                        <button type="button" class="btn btn-success btn-xs" onclick="vistaDirecciones({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Direcciones"></i>&nbsp; Direcciones
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
