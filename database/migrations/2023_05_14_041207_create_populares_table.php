<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopularesTable extends Migration
{
    /**
     * Asigna productos
     *
     * @return void
     */
    public function up()
    {
        Schema::create('populares', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_productos')->unsigned();
            $table->bigInteger('id_servicios')->unsigned();

            $table->integer('posicion');

            $table->foreign('id_productos')->references('id')->on('productos');
            $table->foreign('id_servicios')->references('id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('populares');
    }
}
