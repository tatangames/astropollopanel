<div class="card">
    <div style="float: left">
        <div class="card-header d-flex p-0" style="float: left !important;">
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Carrito de Compras</a></li>
            </ul>
        </div>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">


                <section class="content">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="" onclick="return false;" class="btn btn-primary btn-block mb-3">Categorias</a>

                            <div class="card">

                                <div class="card-body p-0">
                                    <ul class="nav nav-pills flex-column">

                                        @foreach($arrayCategorias as $info)

                                            <li class="nav-item">
                                                <a href="#" class="nav-link" style="color: black; font-weight: bold" onclick="cargarTablaProductos({{ $info->id }});return false;">
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
                                            <p style="font-weight: bold; margin: 16px; color: black !important;">Restaurante: </p> <p style="margin: 16px">{{ $nombreRestaurante }}</p>

                                            <p style="font-weight: bold; margin: 16px; color: black !important;">Cliente: </p> <p style="margin: 16px">{{ $infoDireccion->nombre }}</p>

                                            <p style="font-weight: bold; margin: 16px; color: black !important;">Dirección: </p> <p style="margin: 16px">{{ $infoDireccion->direccion }}</p>

                                            <p style="font-weight: bold; margin: 16px; color: black !important;">Referencia: </p> <p style="margin: 16px">{{ $infoDireccion->punto_referencia }}</p>

                                            <p style="font-weight: bold; margin: 16px; color: black !important;">Teléfono: </p> <p style="margin: 16px">{{ $infoDireccion->telefono }}</p>

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

                                        <div id="divTablaCategoriaProducto">







                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>






                    </div>

                </section>







            </div>

            <!-- LISTA DE CARRITO DE COMPRAS - TABS 2 -->
            <div class="tab-pane" id="tab_2">




                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-body p-0">
                            <div class="card">

                                <div id="divTablaProductoCarritoCompras">



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


                                </div>



                                <center>  <button type="button" class="btn btn-success btn-lg" onclick="enviarOrdenFinal()">
                                        <i class="fa fa-location-arrow" title="Enviar Orden"></i>&nbsp; EnviarOrden
                                    </button>
                                </center>



                            </div>
                        </div>
                    </div>
                </div>







            </div>





            <!-- fin - Tabs -->
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function(){

        var id = {{ $idPrimeraCategoria }};

        cargarTablaProductos(id);
    });

</script>




