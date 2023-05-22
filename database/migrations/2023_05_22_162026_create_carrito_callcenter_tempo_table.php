<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoCallcenterTempoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_callcenter_tempo', function (Blueprint $table) {
            $table->id();

            // CARRITO UNICO PARA CADA USUARIO ADMINISTRADOR TIPO CALL CENTER
            $table->bigInteger('id_callcenter')->unsigned();

            // DIRECCION SELECCIONADA
            $table->bigInteger('id_direccion')->unsigned();

            $table->foreign('id_callcenter')->references('id')->on('administrador');
            $table->foreign('id_direccion')->references('id')->on('direcciones_callcenter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrito_callcenter_tempo');
    }
}
