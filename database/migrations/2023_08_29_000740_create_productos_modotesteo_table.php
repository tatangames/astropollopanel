<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosModotesteoTable extends Migration
{
    /**
     * PARA MOSTRAR AL CLIENTE COMO SE DEBE COMPRAR
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos_modotesteo', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_servicios')->unsigned();
            $table->bigInteger('id_producto')->unsigned();

            $table->integer('posicion');


            $table->foreign('id_servicios')->references('id')->on('servicios');
            $table->foreign('id_producto')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_modotesteo');
    }
}
