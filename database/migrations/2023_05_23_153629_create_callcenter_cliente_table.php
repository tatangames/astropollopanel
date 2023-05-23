<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallcenterClienteTable extends Migration
{
    /**
     * ESTO SE REGISTRA MANUALMENTE, CADA USUARIO DE CALL CENTER ADMINISTRADOR
     * SE LE DEBE ASIGNAR UN CLIENTE PARA QUE SIGA EL PROCESO COMO SI FUERA LA
     * APLICACION
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callcenter_cliente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_administrador')->unsigned();
            $table->bigInteger('id_cliente')->unsigned();

            $table->foreign('id_administrador')->references('id')->on('administrador');
            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('callcenter_cliente');
    }
}
