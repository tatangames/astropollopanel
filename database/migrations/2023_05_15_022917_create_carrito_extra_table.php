<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_extra', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_carrito_temporal')->unsigned();
            $table->bigInteger('id_producto')->unsigned();

            $table->string('nota_producto', 400)->nullable();
            $table->integer('cantidad');

            $table->foreign('id_carrito_temporal')->references('id')->on('carrito_temporal');
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
        Schema::dropIfExists('carrito_extra');
    }
}
