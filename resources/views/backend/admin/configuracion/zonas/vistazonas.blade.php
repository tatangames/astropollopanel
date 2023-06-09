@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Lista de Zonas</h1>
        </div>

        <button type="button" onclick="abrirModalAgregar()" style="margin-top: 20px" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i>
            Nueva Zona
        </button>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-blue">
            <div class="card-header">
                <h3 class="card-title">Zonas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaDatatable">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-actualizar">
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="nombre-nuevo" placeholder="Nombre zona">
                                </div>

                                <div class="form-group">
                                    <label>Descripción de la Zona</label>
                                    <input type="text" maxlength="300" autocomplete="off" class="form-control" id="descripcion-nuevo" placeholder="Descripción zona">
                                </div>


                                <div class="form-group">
                                    <label>Hora Abre la Zona</label>
                                    <input type="time" class="form-control" id="horaabierto-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Hora Cierra la Zona</label>
                                    <input type="time" class="form-control" id="horacerrado-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo extra (tiempo que se agregara a una nueva orden)</label>
                                    <input type="number" value="0" min="0" class="form-control" id="tiempoextra-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="latitud-nuevo" placeholder="Latitud" required>
                                </div>

                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="longitud-nuevo" placeholder="Longitud" required>
                                </div>

                                <hr>


                                <div class="form-group">
                                    <label>Mínimo de compra ($)</label>
                                    <input type="text" class="form-control" autocomplete="off" id="minimocompra-nuevo" placeholder="Mínimo de compra">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="nombre-editar" placeholder="Nombre de Zona">
                                </div>

                                <div class="form-group">
                                    <label>Descripción de la Zona</label>
                                    <input type="text" maxlength="300" autocomplete="off" class="form-control" id="descripcion-editar" placeholder="Descripción zona">
                                </div>


                                <div class="form-group">
                                    <label>Hora Abre la Zona</label>
                                    <input type="time" class="form-control" id="horaabierto-editar">
                                </div>

                                <div class="form-group">
                                    <label>Hora Cierra la Zona</label>
                                    <input type="time" class="form-control" id="horacerrado-editar">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo extra (tiempo que se agregara a una nueva orden)</label>
                                    <input type="number" value="0" autocomplete="off" min="0" class="form-control" id="tiempoextra-editar">
                                </div>

                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="latitud-editar" placeholder="Latitud" required>
                                </div>

                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" autocomplete="off" class="form-control" id="longitud-editar" placeholder="Longitud" required>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <div class="form-group" style="margin-left:20px">
                                    <label>Cierre de Zona</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-problema">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Mensaje de Cierre (Para indicar al cliente el porqué del Cierre)</label>
                                    <input type="text" maxlength="100" autocomplete="off" class="form-control" id="mensaje-editar" placeholder="Explicar al cliente el Cierre">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Disponibilidad Zona (Usuarios no podran ver la zona para crear una dirección)</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label>Mínimo de compra ($)</label>
                                    <input type="text" class="form-control" autocomplete="off" id="minimocompra-editar" placeholder="Mínimo de compra">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardar" onclick="editar()">Guardar</button>
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
            var ruta = "{{ URL::to('admin/zonas/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/zonas/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function modalOpcion(){
            document.getElementById("formulario-opcion").reset();
            $('#modalOpcion').modal('show');
        }

        // informacion zona
        function verInformacion(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/admin/zonas/informacion-zona',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.zona.id);
                        $('#nombre-editar').val(response.data.zona.nombre);

                        $('#horaabierto-editar').val(response.data.zona.hora_abierto_delivery);
                        $('#horacerrado-editar').val(response.data.zona.hora_cerrado_delivery);
                        $('#tiempoextra-editar').val(response.data.zona.tiempo_extra)
                        $('#mensaje-editar').val(response.data.zona.mensaje_bloqueo)
                        $('#latitud-editar').val(response.data.zona.latitud);
                        $('#longitud-editar').val(response.data.zona.longitud);
                        $('#descripcion-editar').val(response.data.zona.descripcion);

                        if(response.data.zona.saturacion === 0){
                            $("#toggle-problema").prop("checked", false);
                        }else{
                            $("#toggle-problema").prop("checked", true);
                        }

                        if(response.data.zona.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }


                        $('#minimocompra-editar').val(response.data.zona.minimo);

                    }else{
                        toastr.error('Zona no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }

        // nueva zona
        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var horaabierto = document.getElementById('horaabierto-nuevo').value;
            var horacerrado = document.getElementById('horacerrado-nuevo').value;
            var tiempoextra = document.getElementById('tiempoextra-nuevo').value;
            var latitud = document.getElementById("latitud-nuevo").value;
            var longitud = document.getElementById("longitud-nuevo").value;
            var descripcion = document.getElementById("descripcion-nuevo").value;

            var minimocompra = document.getElementById("minimocompra-nuevo").value;

            if (descripcion === '') {
                toastr.error("Descripción es requerido");
                return;
            }


            if (nombre === '') {
                toastr.error("Nombre es requerido");
                return;
            }

            if(nombre.length > 50){
                toastr.error("50 caracter máximo nombre");
                return;
            }

            if (horaabierto === '') {
                toastr.error("Horario abierto es requerido");
                return;
            }

            if (horacerrado === '') {
                toastr.error("Horario cerrado es requerido");
                return;
            }

            if(tiempoextra === ''){
                toastr.error("Tiempo Extra es requerido");
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!tiempoextra.match(reglaNumeroEntero)) {
                toastr.error('Tiempo Extra debe ser Entero');
                return;
            }

            if(tiempoextra < 0){
                toastr.error('Tiempo Extra no debe ser negativo');
                return;
            }

            if(tiempoextra > 500){
                toastr.error('Tiempo Extra no debe ser mayor a 500 minutos');
                return;
            }



            if (latitud === '') {
                toastr.error("Latitud es requerido");
                return;
            }

            if(latitud.length > 50){
                toastr.error("50 caracter máximo latitud");
                return;
            }

            if (longitud === '') {
                toastr.error("Longitud es requerido");
                return;
            }

            if(longitud.length > 50){
                toastr.error("50 caracter máximo longitud");
                return;
            }


            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(minimocompra === ''){
                toastr.error('Mínimo de compra es requerido');
                return;
            }

            if(!minimocompra.match(reglaNumeroDosDecimal)) {
                toastr.error('Mínimo de compra debe ser número decimal');
                return;
            }

            if(minimocompra < 0){
                toastr.error('Mínimo de compra no debe ser negativo');
                return;
            }

            if(minimocompra > 1000000){
                toastr.error('Mínimo de compra no debe ser mayor a 1 millón');
                return;
            }


            openLoading();

            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('tiempoextra', tiempoextra);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('minimo', minimocompra);
            formData.append('descripcion', descripcion);

            axios.post('/admin/zonas/registro/nueva', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 0) {
                        toastr.error('Validacion incorrecta');
                    } else if (response.data.success === 1) {
                        toastr.success('Zona Agregada');
                        $('#modalAgregar').modal('hide');
                        recargar();
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


        // editar zona
        function editar() {
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var horaabierto = document.getElementById('horaabierto-editar').value;
            var horacerrado = document.getElementById('horacerrado-editar').value;
            var tiempoextra = document.getElementById('tiempoextra-editar').value;

            var toggleproblema = document.getElementById('toggle-problema').checked;
            var toggleactivo = document.getElementById('toggle-activo').checked;

            var togglep = toggleproblema ? 1 : 0;
            var togglea = toggleactivo ? 1 : 0;

            var latitud = document.getElementById("latitud-editar").value;
            var longitud = document.getElementById("longitud-editar").value;
            var mensaje = document.getElementById("mensaje-editar").value;
            var descripcion = document.getElementById("descripcion-editar").value;

            var minimocompra = document.getElementById("minimocompra-editar").value;


            if (descripcion === '') {
                toastr.error("Descripción es requerido");
                return;
            }



            if (nombre === '') {
                toastr.error("Nombre es requerido");
                return;
            }

            if(nombre.length > 50){
                toastr.error("50 caracter máximo nombre");
                return;
            }

            if(horaabierto === ''){
                toastr.error("Horario abierto es requerido");
                return;
            }

            if(horacerrado === ''){
                toastr.error("Horario abierto es requerido");
                return;
            }

            if (tiempoextra === '') {
                toastr.error("Tiempo Extra es requerido");
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!tiempoextra.match(reglaNumeroEntero)) {
                toastr.error('Tiempo Extra debe ser Entero');
                return;
            }

            if(tiempoextra < 0){
                toastr.error('Tiempo Extra no debe ser negativo');
                return;
            }

            if(tiempoextra > 500){
                toastr.error('Tiempo Extra no debe ser mayor a 500 minutos');
                return;
            }


            if (latitud === '') {
                toastr.error("Latitud es requerido");
                return;
            }

            if(latitud.length > 50){
                toastr.error("50 caracter máximo latitud");
                return;
            }

            if (longitud === '') {
                toastr.error("Longitud es requerido");
                return;
            }

            if(longitud.length > 50){
                toastr.error("50 caracter máximo longitud");
                return;
            }

            if(togglep === 1){
                if(mensaje === ''){
                    toastr.error("Mensaje de Bloqueo es requerido");
                    return;
                }
            }



            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(minimocompra === ''){
                toastr.error('Mínimo de compra es requerido');
                return;
            }

            if(!minimocompra.match(reglaNumeroDosDecimal)) {
                toastr.error('Mínimo de compra debe ser número decimal');
                return;
            }

            if(minimocompra < 0){
                toastr.error('Mínimo de compra no debe ser negativo');
                return;
            }

            if(minimocompra > 1000000){
                toastr.error('Mínimo de compra no debe ser mayor a 1 millón');
                return;
            }



            let formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('tiempoextra', tiempoextra);
            formData.append('togglep', togglep);
            formData.append('togglea', togglea);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('mensaje', mensaje);
            formData.append('minimo', minimocompra);
            formData.append('descripcion', descripcion);
            openLoading();

            axios.post('/admin/zonas/editar-zona', formData, {
            })
                .then((response) => {
                    closeLoading()
                    if (response.data.success === 0) {
                        toastr.error('Validacion incorrecta');
                    } else if (response.data.success === 1) {
                        toastr.success('Zona actualizada');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else if (response.data.success === 2) {
                    // no puede activar la zona porque no tiene poligonos

                        Swal.fire({
                            title: 'Error',
                            text: "No se puede activar la Zona porque no se encuentran Poligonos Registrados",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    }
                    else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }




        function vistaPoligonos(id){
            window.location.href="{{ url('/admin/zonas/poligono') }}/"+id;
        }



        function verMapa(id){
            window.location.href="{{ url('/admin/zonas/ver-mapa/') }}/"+id;
        }






    </script>


@endsection
