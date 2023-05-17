<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesDireccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_direcciones', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_ordenes')->unsigned();

            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('telefono', 10);
            $table->string('referencia', 400)->nullable();
            $table->string('latitud', 50);
            $table->string('longitud', 50);


            $table->string('latitudreal', 50)->nullable();
            $table->string('longitudreal', 50)->nullable();

            // que dispositivo se pidio
            $table->string('appversion', 100);

            $table->foreign('id_ordenes')->references('id')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_direcciones');
    }
}
