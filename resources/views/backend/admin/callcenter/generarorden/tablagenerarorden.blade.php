<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>

                                <th style="width: 10%">Nombre</th>
                                <th style="width: 10%">Nota</th>
                                <th style="width: 10%">Precio</th>
                                <th style="width: 10%">Cantidad</th>
                                <th style="width: 10%">Total</th>

                                <th style="width: 15%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayCarrito as $dato)

                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->nota_producto }}</td>
                                    <td>{{ $dato->precio }}</td>
                                    <td>{{ $dato->cantidad }}</td>
                                    <td>{{ $dato->multiplicado }}</td>

                                    <td>

                                        <button type="button" class="btn btn-success btn-xs" onclick="editarProductoFilaCarrito({{ $dato->id }})">
                                            <i class="fa fa-edit" title="Editar"></i>&nbsp; Editar
                                        </button>

                                        <button type="button" style="margin-left: 8px" class="btn btn-danger btn-xs" onclick="borrarProductoFilaCarrito({{ $dato->id }})">
                                            <i class="fa fa-trash" title="Borrar"></i>&nbsp; Borrar
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


<p style="font-weight: bold; margin-left: 20px; font-size: 18px">Total: {{ $totalCarrito }}</p>
