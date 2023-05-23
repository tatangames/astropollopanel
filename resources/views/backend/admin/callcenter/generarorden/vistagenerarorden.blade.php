@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop


<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1>Generar Orden</h1>
            </div>



        </div>
    </div>
</section>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form>

                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" style="max-width: 350px" id="numero-cliente" class="form-control form-control-lg noEnterSubmit" placeholder="Número Telefónico">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-lg btn-default" onclick="buscarNumero()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<hr>


<!-- SIN DIRECCION ASIGNADA -->



<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaMenuRestaurante">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>








<!-- MODAL NUEVA DIRECCION -->
<div class="modal fade" id="modalDireccionNueva">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Dirección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-direccionnueva">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Restaurante:</label>
                                    <select class="form-control" id="select-servicios">
                                        @foreach($restaurantes as $item)
                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" autocomplete="off" maxlength="100" class="form-control" id="nombre-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="direccion-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" autocomplete="off" maxlength="10" class="form-control" id="telefono-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Punto de Referencia</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="referencia-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarDireccion()">Guardar</button>
            </div>
        </div>
    </div>
</div>



<!-- LISTADO DE DIRECCIONES -->

<div class="modal fade" id="modalListaDirecciones" >
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de Direcciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="formulario-repuesto">
                    <div class="card-body">

                        <div class="form-group">

                            <div class="form-group">
                                <button type="button" class="btn btn-success" onclick="abrirModalNuevaDireccion()">Nueva Dirección</button>
                            </div>


                            <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                                <thead>
                                <tr>
                                    <th style="width: 10%">Cliente</th>
                                    <th style="width: 10%">Dirección</th>
                                    <th style="width: 10%">Referencia</th>
                                    <th style="width: 8%">Restaurante</th>
                                    <th style="width: 5%">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>






                                </tbody>
                            </table>



                        </div>

                    </div>
                </form>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="agregarFila()">Agregar</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modalAgregarCarrito">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar a Carrito</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-carrito">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">


                                <div class="form-group" id="contenedorTieneDescripcion">
                                    <label>Descripción</label>
                                    <textarea type="text" id="textoDescripcion" disabled cols="40" rows="5" autocomplete="off" class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Precio</label>
                                    <input type="hidden" id="idProParaCarrito">
                                    <input type="text" id="textoPrecio" disabled class="form-control">
                                </div>

                                <div class="row mb 2">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Cantidad</label>
                                            <input type="number" id="textoCantidad" min="1" max="100" class="form-control" onchange="multiplicarFilaModal()">
                                        </div>
                                    </div>

                                    <div class="col-sm-4" style="margin-left: 25px">
                                        <div class="form-group">
                                            <label style="font-size: 18px; color: black">Total</label>
                                            <p style="color: black; font-size: 20px; font-weight: bold" id="textoTotal"></p>
                                        </div>

                                    </div>
                                </div>


                                <hr>

                                <div class="form-group" id="contenedorUtilizaNota">
                                    <label style="color: red">Nota es Requerida</label>
                                </div>

                                <div class="form-group">
                                    <label>Nota de Producto</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="textoNotaProducto">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarCarrito()">Guardar</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="modalEditarCarrito">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Cantidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-carrito-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">


                                <div class="form-group" id="contenedorTieneDescripcion-editar">
                                    <label>Descripción</label>
                                    <textarea type="text" id="textoDescripcion-editar" disabled cols="40" rows="5" autocomplete="off" class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Precio</label>
                                    <input type="hidden" id="idProParaCarrito-editar">
                                    <input type="text" id="textoPrecio-editar" disabled class="form-control">
                                </div>

                                <div class="row mb 2">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Cantidad</label>
                                            <input type="number" id="textoCantidad-editar" min="1" max="100" class="form-control" onchange="multiplicarFilaModalEditar()">
                                        </div>
                                    </div>

                                    <div class="col-sm-4" style="margin-left: 25px">
                                        <div class="form-group">
                                            <label style="font-size: 18px; color: black">Total</label>
                                            <p style="color: black; font-size: 20px; font-weight: bold" id="textoTotal-editar"></p>
                                        </div>

                                    </div>
                                </div>


                                <hr>

                                <div class="form-group" id="contenedorUtilizaNota-editar">
                                    <label style="color: red">Nota es Requerida</label>
                                </div>

                                <div class="form-group">
                                    <label>Nota de Producto</label>
                                    <input type="text" autocomplete="off" maxlength="400" class="form-control" id="textoNotaProducto-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarCarritoEditar()">Actualizar</button>
            </div>
        </div>
    </div>
</div>




@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            $('.noEnterSubmit').keypress(function(e){
                if ( e.which == 13 ) return false;
                //or...
                if ( e.which == 13 ) e.preventDefault();
            });


            cargarTablaMenu();
        });

    </script>

    <script>

        function abrirModalNuevaDireccion(){
            $('#modalListaDirecciones').modal('hide');
            var numero = document.getElementById('numero-cliente').value;

            document.getElementById("formulario-direccionnueva").reset();
            $('#telefono-nuevo').val(numero);

            $('#modalDireccionNueva').modal({backdrop: 'static', keyboard: false})
        }

        function cargarTablaMenu(){
            var ruta = "{{ URL::to('admin/callcenter/todo/restaurante/asignado') }}";
            $('#tablaMenuRestaurante').load(ruta);
        }


        // BUSCAR SI CLIENTE TIENE REGISTRO CON SU NUMERO
        function buscarNumero(){

            var numero = document.getElementById('numero-cliente').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(numero === ''){
                toastr.error('Número Telefónico es requerido');
                return;
            }

            if(numero.length > 10){
                toastr.error('Número máximo 10 caracteres');
                return;
            }


            if(!numero.match(reglaNumeroEntero)) {
                toastr.error('Se debe ingresar numeros enteros');
                return;
            }

            if(numero <= 0){
                toastr.error('Número no debe ser negativo o cero');
                return;
            }


            limpiarTablaDirecciones();

            openLoading();

            let formData = new FormData();
            formData.append('numero', numero);


            axios.post('/admin/callcenter/buscar/numero', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // SI HAY DIRECCIONES, CARGAR LA TABLA

                        abrirModalListadoDirecciones(response);

                    }

                    else if (response.data.success === 2) {
                       // NO SE ENCONTRO NUMERO REGISTRADO

                        document.getElementById("formulario-direccionnueva").reset();
                        $('#telefono-nuevo').val(numero);

                        $('#modalDireccionNueva').modal({backdrop: 'static', keyboard: false})

                    }  else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }


        function abrirModalListadoDirecciones(response){


            $.each(response.data.direcciones, function( key, val ){

                var markup = "<tr>" +


                    "<td>" +
                    "<textarea  disabled cols='40' rows='3'  class='form-control' type='text'>" + val.nombre + "</textarea>" +
                    "</td>" +

                    "<td>" +
                    "<textarea  disabled cols='40' rows='5'  class='form-control' type='text'>" + val.direccion + "</textarea>" +
                    "</td>" +


                    "<td>" +
                    "<textarea  disabled cols='40' rows='5' class='form-control' type='text'>" + val.punto_referencia + "</textarea>" +
                    "</td>" +

                    "<td>" +
                    "<textarea  disabled cols='40' rows='3' class='form-control' type='text'>" + val.restaurante + "</textarea>" +
                    "</td>" +

                    "<td>" +
                    "<button type='button' class='btn btn-block btn-success' onclick='preguntarUsarEstaDire("+ val.id + ")'>Seleccionar</button>" +
                    "</td>" +

                    "</tr>";

                $("#matriz tbody").append(markup);


            });


            $('#modalListaDirecciones').modal({backdrop: 'static', keyboard: false})
        }


        function limpiarTablaDirecciones(){

            $("#matriz tbody tr").remove();
        }


        function preguntarUsarEstaDire(id){

            Swal.fire({
                title: 'Seleccionar Dirección?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    crearCarritoConDireccion(id);
                }
            })
        }

        // CREAR CARRITO PARA VER PRODUCTOS DE LA DIRECCION SELECCIONADA
        function crearCarritoConDireccion(id){

            openLoading();

            let formData = new FormData();
            formData.append('id', id);

            axios.post('/admin/callcenter/seleccionar/direccion', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // DIRECCION SELECCIONADA, CARGAR TABLA

                        $('#modalListaDirecciones').modal('hide');
                        cargarTablaMenu();
                    }
                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }



        function preguntaGuardarDireccion(){

            Swal.fire({
                title: 'Guardar Nueva Dirección?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarNuevaDireccion();
                }
            })
        }


        function guardarNuevaDireccion(){

            var restaurante = document.getElementById('select-servicios').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var direccion = document.getElementById('direccion-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var referencia = document.getElementById('referencia-nuevo').value;

            if(restaurante === ''){
                toastr.error('Restaurante es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Dirección es requerido');
                return;
            }

            if(nombre.length > 100){
                toastr.error('100 caracteres para Nombre máximo');
                return;
            }

            if(direccion === ''){
                toastr.error('Dirección es requerido');
                return;
            }

            if(direccion.length > 400){
                toastr.error('400 caracteres para Dirección máximo');
                return;
            }


            if(telefono === ''){
                toastr.error('Teléfono es requerido');
                return;
            }

            if(telefono.length > 10){
                toastr.error('10 caracteres para Teléfono máximo');
                return;
            }


            if(referencia === ''){
                toastr.error('Referencia es requerido');
                return;
            }

            if(referencia.length > 400){
                toastr.error('400 caracteres para Referencia máximo');
                return;
            }

            openLoading();

            let formData = new FormData();
            formData.append('servicio', restaurante);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('direccion', direccion);
            formData.append('referencia', referencia);

            axios.post('/admin/callcenter/guardar/nueva/direccion', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // DIRECCION GUARDADA, HOY MOSTRAR LA TABLA DE PRODUCTOS
                        $('#modalDireccionNueva').modal('hide');

                        // recargar pagina
                        mensajeDirecGuardada();
                    }

                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }


        function mensajeDirecGuardada(){
            Swal.fire({
                title: 'Dirección Guardada',
                text: "Recargar para visualizar los productos del Restaurante",
                icon: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Recargar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload()
                }
            })
        }


        function cargarTablaProductos(id){

            var ruta = "{{ URL::to('admin/callcenter/categoria/productos') }}/" + id;
            $('#divTablaCategoriaProducto').load(ruta);

        }



        function verModalAgregarCarrito(id){
            // viene id producto

            document.getElementById("formulario-carrito").reset();
            openLoading();

            let formData = new FormData();
            formData.append('idproducto', id);

            axios.post('/admin/callcenter/informacion/producto', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        $('#idProParaCarrito').val(id);
                        $('#textoPrecio').val(response.data.producto.precio);
                        $('#textoDescripcion').val(response.data.producto.descripcion);

                        if(response.data.producto.descripcion){
                            document.getElementById("contenedorTieneDescripcion").style.display = "block";
                        }else{
                            document.getElementById("contenedorTieneDescripcion").style.display = "none";
                        }

                        if(response.data.producto.utiliza_nota === 1){
                            document.getElementById("contenedorUtilizaNota").style.display = "block";
                        }else{
                            document.getElementById("contenedorUtilizaNota").style.display = "none";
                        }

                        $('#textoCantidad').val("1");

                        document.getElementById("textoTotal").innerHTML = "$" + response.data.producto.precio;

                        $('#modalAgregarCarrito').modal({backdrop: 'static', keyboard: false})
                    }

                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }


        function multiplicarFilaModal(){
            var reglaNumeroEntero = /^[0-9]\d*$/;

            var textoPrecioFijo = document.getElementById('textoPrecio').value;
            var textoCantidad = document.getElementById('textoCantidad').value;

            if(!textoCantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser Entero');
                return;
            }

            if(textoCantidad <= 0){
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if(textoCantidad > 100){
                toastr.error('Cantidad no debe ser mayor a 100');
                return;
            }

            var multi = textoPrecioFijo * textoCantidad;
            var formateado = '$' + Number(multi).toFixed(2);
            document.getElementById("textoTotal").innerHTML = formateado;
        }


        function multiplicarFilaModalEditar(){
            var reglaNumeroEntero = /^[0-9]\d*$/;

            var textoPrecioFijo = document.getElementById('textoPrecio-editar').value;
            var textoCantidad = document.getElementById('textoCantidad-editar').value;

            if(!textoCantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser Entero');
                return;
            }

            if(textoCantidad <= 0){
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if(textoCantidad > 100){
                toastr.error('Cantidad no debe ser mayor a 100');
                return;
            }

            var multi = textoPrecioFijo * textoCantidad;
            var formateado = '$' + Number(multi).toFixed(2);
            document.getElementById("textoTotal-editar").innerHTML = formateado;
        }


        function preguntaGuardarCarrito(){

            Swal.fire({
                title: 'Guardar Producto?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarProductoEnCarrito();
                }
            })
        }


        function guardarProductoEnCarrito(){

            var idproducto = document.getElementById('idProParaCarrito').value;
            var textoCantidad = document.getElementById('textoCantidad').value;
            var textoNotaProducto = document.getElementById('textoNotaProducto').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!textoCantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser Entero');
                return;
            }

            if(textoCantidad <= 0){
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if(textoCantidad > 100){
                toastr.error('Cantidad no debe ser mayor a 100');
                return;
            }

            if(textoNotaProducto.length > 400){
                toastr.error('Para Nota no debe superar 400 caracteres');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('idproducto', idproducto);
            formData.append('cantidad', textoCantidad);
            formData.append('nota', textoNotaProducto);

            axios.post('/admin/callcenter/guardar/producto/carrito', formData, {
            })
                .then((response) => {
                    closeLoading();

                    console.log(response);

                    if(response.data.success === 1){

                        // GUARDADO
                        toastr.success('Producto Agregado');

                        $('#modalAgregarCarrito').modal('hide');

                        recargarTablaCarritoCompras();

                    }
                    else if(response.data.success === 2){
                        // NO HAY CARRITO CREADO
                        alertaCarritoNoEncontrado();

                    }
                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }


        function alertaCarritoNoEncontrado(){

            Swal.fire({
                title: 'Error al Guardar',
                text: "No se encontro el carrito de compras",
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Recargar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload()
                }
            })
        }

        function borrarProductoFilaCarrito(id){

            Swal.fire({
                title: 'Borrar Producto',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarFilaProducto(id);
                }
            })
        }

        function borrarFilaProducto(id){

            openLoading();

            let formData = new FormData();
            formData.append('idfila', id);

            axios.post('/admin/callcenter/borrar/producto/carrito', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // GUARDADO
                        toastr.success('Producto Borrado');

                        recargarTablaCarritoCompras();

                    }

                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });


        }


        function recargarTablaCarritoCompras(){

            var ruta = "{{ URL::to('admin/callcenter/recargar/tabla/carrito') }}";
            $('#divTablaProductoCarritoCompras').load(ruta);
        }




        // ESTO ELIMINA CARRITO DE COMPRAS Y QUITA DIRECCION SELECCIONADA
        function borrarTodoEstadoCarrito(){

            Swal.fire({
                title: 'Borrar Carrito',
                text: "Esto deselecciona la Dirección del cliente",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarCarritoTodo();
                }
            })

        }


        function eliminarCarritoTodo(){

            openLoading();

            axios.post('/admin/callcenter/borrar/todoel/carrito',{
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // GUARDADO
                        alertaCarritoBorrado();
                    }

                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }


        function alertaCarritoBorrado(){
            Swal.fire({
                title: 'Carrito Borrado',
                text: "Se debe Recargar la Página",
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Recargar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload()
                }
            })
        }




        function editarProductoFilaCarrito(id){


            openLoading();
            document.getElementById("formulario-carrito-editar").reset();
            let formData = new FormData();
            formData.append('idfila', id);

            axios.post('/admin/callcenter/informacion/producto/carrito', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // MODAL EDITAR PRODUCTO FILA DEL CARRITO

                        $('#idProParaCarrito-editar').val(id);
                        $('#textoPrecio-editar').val(response.data.producto.precio);
                        $('#textoDescripcion-editar').val(response.data.producto.descripcion);

                        if(response.data.producto.descripcion){
                            document.getElementById("contenedorTieneDescripcion-editar").style.display = "block";
                        }else{
                            document.getElementById("contenedorTieneDescripcion-editar").style.display = "none";
                        }

                        if(response.data.producto.utiliza_nota === 1){
                            document.getElementById("contenedorUtilizaNota-editar").style.display = "block";
                        }else{
                            document.getElementById("contenedorUtilizaNota-editar").style.display = "none";
                        }

                        $('#textoCantidad-editar').val(response.data.info.cantidad);

                        document.getElementById("textoTotal-editar").innerHTML = response.data.multiplicado;

                        $('#modalEditarCarrito').modal({backdrop: 'static', keyboard: false})
                    }

                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }



        function preguntaGuardarCarritoEditar(){

            Swal.fire({
                title: 'Actualizar Carrito',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    actualizarDatosCarritoFila();
                }
            })
        }


        function actualizarDatosCarritoFila(){

            var idfila = document.getElementById('idProParaCarrito-editar').value;
            var textoCantidad = document.getElementById('textoCantidad-editar').value;
            var textoNotaProducto = document.getElementById('textoNotaProducto-editar').value;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!textoCantidad.match(reglaNumeroEntero)) {
                toastr.error('Cantidad debe ser Entero');
                return;
            }

            if(textoCantidad <= 0){
                toastr.error('Cantidad no debe ser negativo');
                return;
            }

            if(textoCantidad > 100){
                toastr.error('Cantidad no debe ser mayor a 100');
                return;
            }

            if(textoNotaProducto.length > 400){
                toastr.error('Para Nota no debe superar 400 caracteres');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('idfila', idfila);
            formData.append('cantidad', textoCantidad);
            formData.append('nota', textoNotaProducto);

            axios.post('/admin/callcenter/actualizar/fila/carrito', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // GUARDADO
                        toastr.success('Actualizado');

                        $('#modalEditarCarrito').modal('hide');

                        recargarTablaCarritoCompras();

                    }
                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }




        function enviarOrdenFinal(){

            Swal.fire({
                title: 'Enviar Orden',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    peticionEnviarOrden();
                }
            })
        }


        function peticionEnviarOrden(){

            openLoading();

            axios.post('/admin/callcenter/enviar/orden', {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // ORDEN ENVIADA COR

                    }
                    else {
                        toastr.error('Error al guardar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    closeLoading();
                });
        }





    </script>




@endsection
