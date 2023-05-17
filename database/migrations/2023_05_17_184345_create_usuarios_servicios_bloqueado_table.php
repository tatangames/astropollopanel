<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosServiciosBloqueadoTable extends Migration
{
    /**
     * AQUI ENTRAN LOS USUARIOS DE RESTAURANTES BLOQUEADOS
     * NO PODRAN RESPONDER ORDENES O EDITAR OTROS DATOS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_servicios_bloqueado', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuario_servicios')->unsigned();

            $table->foreign('id_usuario_servicios')->references('id')->on('usuarios_servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_servicios_bloqueado');
    }
}
