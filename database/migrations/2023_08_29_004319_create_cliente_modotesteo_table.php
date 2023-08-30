<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteModotesteoTable extends Migration
{
    /**
     * CLIENTE REGISTRA SU ID PARA QUE NO APARESCA BOTON PRUEBA DE ORDEN
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente_modotesteo', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_cliente')->unsigned();
            $table->dateTime('fecha');


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
        Schema::dropIfExists('cliente_modotesteo');
    }
}
