<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoCallcenterExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_callcenter_extra', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_carrito_call_tempo')->unsigned();
            $table->bigInteger('id_producto')->unsigned();

            $table->string('nota_producto', 400)->nullable();
            $table->integer('cantidad');

            $table->foreign('id_carrito_call_tempo')->references('id')->on('carrito_callcenter_tempo');
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
        Schema::dropIfExists('carrito_callcenter_extra');
    }
}
