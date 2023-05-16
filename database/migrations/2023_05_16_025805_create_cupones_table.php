<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponesTable extends Migration
{
    /**
     * CUPONES
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tipo_cupon')->unsigned();

            // IDENTIFICADOR DEL CUPON
            $table->string('texto_cupon', 50)->unique();

            $table->integer('uso_limite');
            $table->integer('contador');
            $table->boolean('activo');

            $table->foreign('id_tipo_cupon')->references('id')->on('tipo_cupon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupones');
    }
}
