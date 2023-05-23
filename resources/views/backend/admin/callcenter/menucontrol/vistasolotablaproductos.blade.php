<table id="tablaProductos" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 12%">Producto</th>
        <th style="width: 12%">Descripción</th>
        <th style="width: 6%">Precio</th>
        <th style="width: 8%">Imagen</th>
        <th style="width: 6%">Opciones</th>
    </tr>
    </thead>
    <tbody>

    @foreach($arrayProductos as $dato)

        <tr>

            <td>{{ $dato->nombre }}</td>

            <td>
                <textarea  disabled cols="40" rows="5" class="form-control" type="text">{{ $dato->descripcion }}</textarea>
            </td>

            <td>{{ $dato->precio }}</td>

            <td>
                @if($dato->utiliza_imagen == 1)
                    <center><img alt="Imagenes" src="{{ url('storage/imagenes/'.$dato->imagen) }}" width="90px" height="90px" /></center>
                @endif
            </td>

            <td>
                <button type="button" class="btn btn-success btn-xs" onclick="verModalAgregarCarrito({{ $dato->id }})">
                    <i class="fa fa-plus" title="Agregar"></i>&nbsp; Agregar
                </button>
            </td>

        </tr>

    @endforeach


    </tbody>
</table>


<script>
    $(function () {
        $("#tablaProductos").DataTable({
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
