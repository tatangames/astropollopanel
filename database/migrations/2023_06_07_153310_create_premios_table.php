<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiosTable extends Migration
{
    /**
     * LISTA DE PREMIOS PARA CADA RESTAURANTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premios', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_servicio')->unsigned();

            // nombre del premio
            $table->string('nombre', 150);

            // costo en puntos
            $table->integer('puntos');

            // activo / inactivo
            $table->boolean('activo');


            $table->foreign('id_servicio')->references('id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('premios');
    }
}
