<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesPremiosTable extends Migration
{
    /**
     * PARA QUE EL CLIENTE PUEDA SELECCIONAR EL PREMIO QUE LE CORRESPONDE
     * ESTO SE DEBE BORRAR AL CAMBIAR DE DIRECCION
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_premios', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_clientes')->unsigned();
            $table->bigInteger('id_premios')->unsigned();

            $table->foreign('id_clientes')->references('id')->on('clientes');
            $table->foreign('id_premios')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_premios');
    }
}
