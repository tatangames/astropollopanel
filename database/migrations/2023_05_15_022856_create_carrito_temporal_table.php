<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoTemporalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_temporal', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_clientes')->unsigned();
            $table->bigInteger('id_servicios')->unsigned();

            $table->foreign('id_clientes')->references('id')->on('clientes');
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
        Schema::dropIfExists('carrito_temporal');
    }
}
