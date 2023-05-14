<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasPrincipalesTable extends Migration
{
    /**
     * son las categorias que se muestran pantalla principal app
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_principales', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_servicios')->unsigned();
            $table->bigInteger('id_categorias')->unsigned();

            $table->integer('posicion');

            $table->foreign('id_servicios')->references('id')->on('servicios');
            $table->foreign('id_categorias')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias_principales');
    }
}
