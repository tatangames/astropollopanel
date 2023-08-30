<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);

            // si utiliza cupon
            $table->boolean('utiliza_cupon');


            // SI EL RESTAURANTE MOSTRARA BOTON PARA PRIMERA COMPRA
            // 0: no
            // 1: si
            $table->boolean('modo_prueba');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
