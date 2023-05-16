<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCProductoGratisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_producto_gratis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cupones')->unsigned();
            $table->bigInteger('id_servicios')->unsigned();

            // NOMBRE DE PRODUCTO GRATIS
            $table->string('nombre', 100);

            $table->foreign('id_cupones')->references('id')->on('cupones');
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
        Schema::dropIfExists('c_producto_gratis');
    }
}
