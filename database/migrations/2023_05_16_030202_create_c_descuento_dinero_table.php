<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCDescuentoDineroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_descuento_dinero', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cupones')->unsigned();
            $table->bigInteger('id_servicios')->unsigned();

            // DINERO A DESCONTAR
            $table->decimal('dinero', 10,2);

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
        Schema::dropIfExists('c_descuento_dinero');
    }
}
