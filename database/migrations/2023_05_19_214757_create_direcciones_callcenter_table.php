<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionesCallcenterTable extends Migration
{
    /**
     * DIRECCIIONES QUE GUARDARA EL CALL CENTER
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direcciones_callcenter', function (Blueprint $table) {
            $table->id();

            // PARA PODER VISUALIZAR LOS PRODUCTOS SEGUN RESTAURANTE
            $table->bigInteger('id_servicios')->unsigned();
            $table->bigInteger('id_zonas')->unsigned()->nullable();

            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('punto_referencia', 400)->nullable();
            $table->string('telefono', 10);

            $table->foreign('id_servicios')->references('id')->on('servicios');
            $table->foreign('id_zonas')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direcciones_callcenter');
    }
}
