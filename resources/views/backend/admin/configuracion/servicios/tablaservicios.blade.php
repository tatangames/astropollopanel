<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th style="width: 10%">Nombre</th>
                            <th style="width: 6%">Teléfono </th>
                            <th style="width: 8%">Usa Mínimo $</th>
                            <th style="width: 8%">Minimo Compra</th>
                            <th style="width: 12%">Tiempo Preparación (Minutos)</th>
                            <th style="width: 10%">Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($servicios as $dato)
                            <tr>
                                <td>{{ $dato->nombre }}</td>
                                <td>{{ $dato->telefono }}</td>

                                <td>
                                    @if($dato->utiliza_minimo == 0)
                                        <span class="badge bg-danger">No</span>
                                    @else
                                        <span class="badge bg-success">Si</span>
                                    @endif
                                </td>
                                <td>{{ $dato->minimo }}</td>
                                <td>{{ $dato->tiempo }}</td>

                                <td>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="informacionServicio({{ $dato->id }})">
                                        <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                                    </button>

                                    <br><br>

                                    <button type="button" class="btn btn-success btn-xs" onclick="modalHorario({{ $dato->id }})">
                                        <i class="fas fa-eye" title="Horarios"></i>&nbsp; Horarios
                                    </button>
                                    <br><br>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="verCategorias({{ $dato->id }})">
                                        <i class="fas fa-eye" title="Categorias"></i>&nbsp; Categorias
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
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas"
            }
        });
    });
</script>
