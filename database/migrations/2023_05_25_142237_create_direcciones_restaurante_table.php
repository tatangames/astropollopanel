<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionesRestauranteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direcciones_restaurante', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_servicio')->unsigned();
            $table->string('direccion', 600);

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
        Schema::dropIfExists('direcciones_restaurante');
    }
}
