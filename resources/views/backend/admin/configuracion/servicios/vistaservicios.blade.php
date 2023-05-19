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
            <div class="col-sm-12">
                <h1>Restaurantes</h1>
            </div>
            <div style="margin-top:15px; margin-left:15px">

                <button type="button" onclick="modalNuevo()" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i>
                    Nuevo Restaurante
                </button>

            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-blue">
            <div class="card-header">
                <h3 class="card-title">Lista de Restaurante Registrados</h3>
            </div>
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
</section>

<!-- modal agregar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Restaurante</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Nombre del Restaurante</label>
                                        <input type="text" maxlength="100" autocomplete="off" class="form-control" id="nombre-nuevo" placeholder="Nombre del Restaurante">
                                    </div>

                                    <div class="form-group">
                                        <label>Utiliza Cupón</label><br>
                                        <label class="switch" style="margin-top:10px">
                                            <input type="checkbox" id="toggle-cupon">
                                            <div class="slider round">
                                                <span class="on">Si</span>
                                                <span class="off">No</span>
                                            </div>
                                        </label>
                                    </div>


                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <p>Horarios</p>

                                    <!-- horario abre y cierre -->
                                    <div class="form-group">
                                        <label>Cerrado lunes</label>
                                        <input type="checkbox" id="cbcerradolunes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Lunes</label>
                                        <input type="time" class="form-control" id="horalunes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Lunes</label>
                                        <input type="time" class="form-control" id="horalunes2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado martes</label>
                                        <input type="checkbox" id="cbcerradomartes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Martes</label>
                                        <input type="time" class="form-control" id="horamartes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Martes</label>
                                        <input type="time" class="form-control" id="horamartes2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado miercoles</label>
                                        <input type="checkbox" id="cbcerradomiercoles">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado jueves</label>
                                        <input type="checkbox" id="cbcerradojueves">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Jueves</label>
                                        <input type="time" class="form-control" id="horajueves1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Jueves</label>
                                        <input type="time" class="form-control" id="horajueves2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado viernes</label>
                                        <input type="checkbox" id="cbcerradoviernes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado Sabado</label>
                                        <input type="checkbox" id="cbcerradosabado">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Sabado</label>
                                        <input type="time" class="form-control" id="horasabado1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Sabado</label>
                                        <input type="time" class="form-control" id="horasabado2">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado Domingo</label>
                                        <input type="checkbox" id="cbcerradodomingo">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo2">
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="guardarServicio()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal editar servicio-->
<div class="modal fade" id="modalServicio">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Restaurante</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-servicio">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>Nombre del Restaurante</label>

                                <input type="hidden" id="id-editar-servicio">
                                <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre del Restaurante">
                            </div>

                            <div class="form-group">
                                <label>Utiliza Cupón</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-cupon-editar">
                                    <div class="slider round">
                                        <span class="on">Si</span>
                                        <span class="off">No</span>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editarservicio()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar horarios-->
<div class="modal fade" id="modalHorario">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Horarios</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-horario">
                    <div class="card-body">
                        <div class="col-md-12">


                            <input type="hidden" id="id-editar-horario">

                            <!-- horario abre y cierre -->
                            <div class="form-group">
                                <label>Cerrado lunes</label>
                                <br>
                                <input type="checkbox" id="cbcerradolunes-editar">
                            </div>

                            <div class="form-group">
                                <label>Horario abre Lunes</label>
                                <input type="time" class="form-control" id="horalunes1-editar">
                            </div>

                            <div class="form-group">
                                <label>Horario cierra Lunes</label>
                                <input type="time" class="form-control" id="horalunes2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado martes</label>
                                <br>
                                <input type="checkbox" id="cbcerradomartes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Martes</label>
                                <input type="time" class="form-control" id="horamartes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Martes</label>
                                <input type="time" class="form-control" id="horamartes2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado miercoles</label>
                                <br>
                                <input type="checkbox" id="cbcerradomiercoles-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado jueves</label>
                                <br>
                                <input type="checkbox" id="cbcerradojueves-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Jueves</label>
                                <input type="time" class="form-control" id="horajueves1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Jueves</label>
                                <input type="time" class="form-control" id="horajueves2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado viernes</label>
                                <br>
                                <input type="checkbox" id="cbcerradoviernes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Viernes</label>
                                <input type="time" class="form-control" id="horaviernes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Viernes</label>
                                <input type="time" class="form-control" id="horaviernes2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado Sabado</label>
                                <br>
                                <input type="checkbox" id="cbcerradosabado-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Sabado</label>
                                <input type="time" class="form-control" id="horasabado1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Sabado</label>
                                <input type="time" class="form-control" id="horasabado2-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado Domingo</label>
                                <br>
                                <input type="checkbox" id="cbcerradodomingo-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Domingo</label>
                                <input type="time" class="form-control" id="horadomingo1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Domingo</label>
                                <input type="time" class="form-control" id="horadomingo2-editar">
                            </div>


                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editarHoras()">Guardar</button>

            </div>
        </div>
    </div>
</div>




<!-- opciones de modales -->
<div class="modal fade" id="modalOpcion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Opciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-opciones">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="id-opciones">

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verCategorias()">
                                        <i class="fas fa-pencil-alt"></i>
                                        Categorias
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verSlider()">
                                        <i class="fas fa-pencil-alt"></i>
                                        Banner
                                    </button>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verCategoriasPrincipales()">
                                        <i class="fas fa-pencil-alt"></i>
                                        Categorías Principales
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verProductosPrincipales()">
                                        <i class="fas fa-pencil-alt"></i>
                                        Productos Principales
                                    </button>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verCuponesProGratis()">
                                        <i class="fas fa-info"></i>
                                        Cupones de Productos Gratis
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verCuponesDescuentoDinero()">
                                        <i class="fas fa-info"></i>
                                        Cupones de Descuento de Dinero
                                    </button>
                                </div>

                                <div class="form-group">
                                    <button class="form-control btn btn-info btn-sm" type="button" onclick="verCuponesDescuentoPorcentaje()">
                                        <i class="fas fa-info"></i>
                                        Cupones de Descuento de Porcentaje
                                    </button>
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
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
            var ruta = "{{ URL::to('admin/servicios/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });

    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('admin/servicios/listado/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();

            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function guardarServicio(){
            Swal.fire({
                title: 'Guardar Servicio?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevoRegistro();
                }
            })
        }

        //nuevo servicio
        function nuevoRegistro(){


            var nombre = document.getElementById('nombre-nuevo').value;

            var cbcupon = document.getElementById('toggle-cupon').checked;

            // minimo de compra
            var toggleCupon = cbcupon ? 1 : 0;




            var horalunes1 = document.getElementById('horalunes1').value;
            var horalunes2 = document.getElementById('horalunes2').value;
            var cbcerradolunes = document.getElementById('cbcerradolunes').checked;

            var toggleLunes = cbcerradolunes ? 1 : 0;

            var horamartes1 = document.getElementById('horamartes1').value;
            var horamartes2 = document.getElementById('horamartes2').value;
            var cbcerradomartes = document.getElementById('cbcerradomartes').checked;

            var toggleMartes = cbcerradomartes ? 1 : 0;

            var horamiercoles1 = document.getElementById('horamiercoles1').value;
            var horamiercoles2 = document.getElementById('horamiercoles2').value;
            var cbcerradomiercoles = document.getElementById('cbcerradomiercoles').checked;

            var toggleMiercoles = cbcerradomiercoles ? 1 : 0;

            var horajueves1 = document.getElementById('horajueves1').value;
            var horajueves2 = document.getElementById('horajueves2').value;
            var cbcerradojueves = document.getElementById('cbcerradojueves').checked;

            var toggleJueves = cbcerradojueves ? 1 : 0;

            var horaviernes1 = document.getElementById('horaviernes1').value;
            var horaviernes2 = document.getElementById('horaviernes2').value;
            var cbcerradoviernes = document.getElementById('cbcerradoviernes').checked;

            var toggleViernes = cbcerradoviernes ? 1 : 0;

            var horasabado1 = document.getElementById('horasabado1').value;
            var horasabado2 = document.getElementById('horasabado2').value;
            var cbcerradosabado = document.getElementById('cbcerradosabado').checked;

            var toggleSabado = cbcerradosabado ? 1 : 0;

            var horadomingo1 = document.getElementById('horadomingo1').value;
            var horadomingo2 = document.getElementById('horadomingo2').value;
            var cbcerradodomingo = document.getElementById('cbcerradodomingo').checked;

            var toggleDomingo = cbcerradodomingo ? 1 : 0;


            if (nombre === '') {
                toastr.error("Nombre de Negocio es requerido");
                return;
            }

            if(nombre.length > 100){
                toastr.error("100 caracteres máximo para nombre");
                return;
            }



            // VALIDACION DE HORARIOS


            if (horalunes1 === '') {
                toastr.error("Lunes (Abre) horario es requerido");
                return;
            }

            if (horalunes2 === '') {
                toastr.error("Lunes (Cierre) horario es requerido");
                return;
            }


            //------

            if (horamartes1 === '') {
                toastr.error("Martes (Abre) horario es requerido");
                return;
            }

            if (horamartes2 === '') {
                toastr.error("Martes (Cierre) horario es requerido");
                return;
            }



            //---

            if (horamiercoles1 === '') {
                toastr.error("Miercoles (Abre) horario es requerido");
                return;
            }

            if (horamiercoles2 === '') {
                toastr.error("Miercoles (Cierre) horario es requerido");
                return;
            }


            //----

            if (horajueves1 === '') {
                toastr.error("Jueves (Abre) horario es requerido");
                return;
            }

            if (horajueves2 === '') {
                toastr.error("Jueves (Cierre) horario es requerido");
                return;
            }


            //---

            if (horaviernes1 === '') {
                toastr.error("Viernes (Abre) horario es requerido");
                return;
            }

            if (horaviernes2 === '') {
                toastr.error("Viernes (Cierre) horario es requerido");
                return;
            }


            //---

            if (horasabado1 === '') {
                toastr.error("Sabado (Abre) horario es requerido");
                return;
            }

            if (horasabado2 === '') {
                toastr.error("Sabado (Cierre) horario es requerido");
                return;
            }


            //---

            if (horadomingo1 === '') {
                toastr.error("Domingo (Abre) horario es requerido");
                return;
            }

            if (horadomingo2 === '') {
                toastr.error("Domingo (Cierre) horario es requerido");
                return;
            }


            openLoading();


                var formData = new FormData();
                formData.append('nombre', nombre);

                formData.append('togglecupon', toggleCupon);

                formData.append('horalunes1', horalunes1);
                formData.append('horalunes2', horalunes2);
                formData.append('cbcerradolunes', toggleLunes);

                formData.append('horamartes1', horamartes1);
                formData.append('horamartes2', horamartes2);
                formData.append('cbcerradomartes', toggleMartes);

                //---

                formData.append('horamiercoles1', horamiercoles1);
                formData.append('horamiercoles2', horamiercoles2);
                formData.append('cbcerradomiercoles', toggleMiercoles);

                //--

                formData.append('horajueves1', horajueves1);
                formData.append('horajueves2', horajueves2);
                formData.append('cbcerradojueves', toggleJueves);

                formData.append('horaviernes1', horaviernes1);
                formData.append('horaviernes2', horaviernes2);
                formData.append('cbcerradoviernes', toggleViernes);

                //---

                formData.append('horasabado1', horasabado1);
                formData.append('horasabado2', horasabado2);
                formData.append('cbcerradosabado', toggleSabado);

                formData.append('horadomingo1', horadomingo1);
                formData.append('horadomingo2', horadomingo2);
                formData.append('cbcerradodomingo', toggleDomingo);

                axios.post('/admin/servicios/registrar/nuevo', formData, {
                })
                    .then((response) => {
                        closeLoading();

                        if (response.data.success === 1) {
                            toastr.success('Servicio Agregado');
                            $('#modalAgregar').modal('hide');
                            recargar();
                        }
                        else {
                            toastr.error('Error al guardar');
                        }

                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Error al guardar');
                    });

        }


        // vista editar servicio
        function informacionServicio(id){

            document.getElementById("formulario-servicio").reset();
            openLoading();

            axios.post('/admin/servicios/informacion',{
                'id': id
            })
                .then((response) => {
                   closeLoading();

                    if(response.data.success === 1){

                        $('#modalServicio').modal('show');

                        $('#id-editar-servicio').val(id);
                        $('#nombre-editar').val(response.data.servicio.nombre);

                        if(response.data.servicio.utiliza_cupon === 0){
                            $("#toggle-cupon-editar").prop("checked", false);
                        }else{
                            $("#toggle-cupon-editar").prop("checked", true);
                        }

                    }else{
                        toastr.error('Informacion no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }

        // abrir modales de opciones de servicio
        function abrirModalOpciones(id){

            document.getElementById("formulario-opciones").reset();

            $('#id-opciones').val(id);
            $('#modalOpcion').modal('show');
        }


        // editar servicio
        function editarservicio(){

            var id = document.getElementById('id-editar-servicio').value;
            var nombre = document.getElementById('nombre-editar').value;

            var cbcupon = document.getElementById('toggle-cupon-editar').checked;

            var toggleCupon = cbcupon ? 1 : 0;


            if (nombre === '') {
                toastr.error("Nombre de Negocio es requerido");
                return;
            }

            if(nombre.length > 100){
                toastr.error("100 caracteres máximo para nombre");
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('togglecupon', toggleCupon);

                axios.post('/admin/servicios/editar-servicio', formData, {
                })
                    .then((response) => {
                       closeLoading();

                        if (response.data.success === 1) {
                            toastr.success('Servicio Actualizado');
                            $('#modalServicio').modal('hide');
                            recargar();
                        }
                        else {
                            toastr.error('Error al guardar');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Error de servidor');
                    });
        }


        // vista editar horarios
        function modalHorario(id){
            document.getElementById("formulario-horario").reset();

            openLoading();

            axios.post('/admin/servicios/informacion-horario/servicio',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        $('#modalHorario').modal('show');

                        $('#id-editar-horario').val(id);

                        $.each(response.data.horario, function( key, val ){
                            if(val.dia == 1){ //domingo
                                $('#horadomingo1-editar').val(val.hora1);
                                $('#horadomingo2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradodomingo-editar').prop('checked', true);
                                }

                            }else if(val.dia == 2){
                                $('#horalunes1-editar').val(val.hora1);
                                $('#horalunes2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradolunes-editar').prop('checked', true);
                                }
                            }else if(val.dia == 3){
                                $('#horamartes1-editar').val(val.hora1);
                                $('#horamartes2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradomartes-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia == 4){
                                $('#horamiercoles1-editar').val(val.hora1);
                                $('#horamiercoles2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradomiercoles-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia == 5){
                                $('#horajueves1-editar').val(val.hora1);
                                $('#horajueves2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradojueves-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia == 6){
                                $('#horaviernes1-editar').val(val.hora1);
                                $('#horaviernes2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradoviernes-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia == 7){
                                $('#horasabado1-editar').val(val.hora1);
                                $('#horasabado2-editar').val(val.hora2);

                                if(val.cerrado == 1){
                                    $('#cbcerradosabado-editar').prop('checked', true);
                                }
                            }

                        });

                    }else{
                        toastr.error('Horario no encontrado');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error del servidor');
                });
        }

        // ditar las horas del servicio
        function editarHoras(){
            var id = document.getElementById('id-editar-horario').value;

            var horalunes1 = document.getElementById('horalunes1-editar').value;
            var horalunes2 = document.getElementById('horalunes2-editar').value;
            var cbcerradolunes = document.getElementById('cbcerradolunes-editar').checked;

            var horamartes1 = document.getElementById('horamartes1-editar').value;
            var horamartes2 = document.getElementById('horamartes2-editar').value;

            var cbcerradomartes = document.getElementById('cbcerradomartes-editar').checked;

            var horamiercoles1 = document.getElementById('horamiercoles1-editar').value;
            var horamiercoles2 = document.getElementById('horamiercoles2-editar').value;
            var cbcerradomiercoles = document.getElementById('cbcerradomiercoles-editar').checked;

            var horajueves1 = document.getElementById('horajueves1-editar').value;
            var horajueves2 = document.getElementById('horajueves2-editar').value;
            var cbcerradojueves = document.getElementById('cbcerradojueves-editar').checked;

            var horaviernes1 = document.getElementById('horaviernes1-editar').value;
            var horaviernes2 = document.getElementById('horaviernes2-editar').value;
            var cbcerradoviernes = document.getElementById('cbcerradoviernes-editar').checked;

            var horasabado1 = document.getElementById('horasabado1-editar').value;
            var horasabado2 = document.getElementById('horasabado2-editar').value;
            var cbcerradosabado = document.getElementById('cbcerradosabado-editar').checked;

            var horadomingo1 = document.getElementById('horadomingo1-editar').value;
            var horadomingo2 = document.getElementById('horadomingo2-editar').value;
            var cbcerradodomingo = document.getElementById('cbcerradodomingo-editar').checked;

            var toggleLunes = cbcerradolunes ? 1 : 0;
            var toggleMartes = cbcerradomartes ? 1 : 0;
            var toggleMiercoles = cbcerradomiercoles ? 1 : 0;
            var toggleJueves = cbcerradojueves ? 1 : 0;
            var toggleViernes = cbcerradoviernes ? 1 : 0;
            var toggleSabado = cbcerradosabado ? 1 : 0;
            var toggleDomingo = cbcerradodomingo ? 1 : 0;


            // VALIDACION DE HORARIOS


            if (horalunes1 === '') {
                toastr.error("Lunes (Abre) horario es requerido");
                return;
            }

            if (horalunes2 === '') {
                toastr.error("Lunes (Cierre) horario es requerido");
                return;
            }


            //------

            if (horamartes1 === '') {
                toastr.error("Martes (Abre) horario es requerido");
                return;
            }

            if (horamartes2 === '') {
                toastr.error("Martes (Cierre) horario es requerido");
                return;
            }



            //---

            if (horamiercoles1 === '') {
                toastr.error("Miercoles (Abre) horario es requerido");
                return;
            }

            if (horamiercoles2 === '') {
                toastr.error("Miercoles (Cierre) horario es requerido");
                return;
            }


            //----

            if (horajueves1 === '') {
                toastr.error("Jueves (Abre) horario es requerido");
                return;
            }

            if (horajueves2 === '') {
                toastr.error("Jueves (Cierre) horario es requerido");
                return;
            }


            //---

            if (horaviernes1 === '') {
                toastr.error("Viernes (Abre) horario es requerido");
                return;
            }

            if (horaviernes2 === '') {
                toastr.error("Viernes (Cierre) horario es requerido");
                return;
            }


            //---

            if (horasabado1 === '') {
                toastr.error("Sabado (Abre) horario es requerido");
                return;
            }

            if (horasabado2 === '') {
                toastr.error("Sabado (Cierre) horario es requerido");
                return;
            }


            //---

            if (horadomingo1 === '') {
                toastr.error("Domingo (Abre) horario es requerido");
                return;
            }

            if (horadomingo2 === '') {
                toastr.error("Domingo (Cierre) horario es requerido");
                return;
            }


            openLoading();
            var formData = new FormData();

            formData.append('id', id);
            formData.append('horalunes1', horalunes1);
            formData.append('horalunes2', horalunes2);
            formData.append('cbcerradolunes', toggleLunes);

            formData.append('horamartes1', horamartes1);
            formData.append('horamartes2', horamartes2);
            formData.append('cbcerradomartes', toggleMartes);

            //---

            formData.append('horamiercoles1', horamiercoles1);
            formData.append('horamiercoles2', horamiercoles2);
            formData.append('cbcerradomiercoles', toggleMiercoles);

            //--

            formData.append('horajueves1', horajueves1);
            formData.append('horajueves2', horajueves2);
            formData.append('cbcerradojueves', toggleJueves);

            formData.append('horaviernes1', horaviernes1);
            formData.append('horaviernes2', horaviernes2);
            formData.append('cbcerradoviernes', toggleViernes);

            //---

            formData.append('horasabado1', horasabado1);
            formData.append('horasabado2', horasabado2);
            formData.append('cbcerradosabado', toggleSabado);

            formData.append('horadomingo1', horadomingo1);
            formData.append('horadomingo2', horadomingo2);
            formData.append('cbcerradodomingo', toggleDomingo);

            axios.post('/admin/servicios/editar/horarios', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado');
                        $('#modalHorario').modal('hide');
                    }
                    else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error');
                });
        }

        function verCategorias(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/categorias/listado') }}/"+id;
        }

        function verSlider(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/slider/listado') }}/"+id;
        }


        function verCuponesProGratis(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/cupones/servicio/progratis') }}/"+id;
        }

        function verCuponesDescuentoDinero(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/cupones/servicio/descdinero') }}/"+id;
        }

        function verCuponesDescuentoPorcentaje(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/cupones/servicio/descporcentaje') }}/"+id;
        }


        function verCategoriasPrincipales(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/categorias/servicio/principales') }}/"+id;
        }


        function verProductosPrincipales(){
            var id = document.getElementById('id-opciones').value;
            window.location.href="{{ url('/admin/productos/servicio/principales') }}/"+id;
        }



    </script>

@endsection
