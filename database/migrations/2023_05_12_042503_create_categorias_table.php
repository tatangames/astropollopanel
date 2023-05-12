<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_servicios')->unsigned();

            $table->string('nombre', 200);
            $table->integer('posicion');
            $table->boolean('activo');

            $table->boolean('usa_horario');
            $table->time('hora_abre');
            $table->time('hora_cierra');
            $table->string('imagen', 100);

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
        Schema::dropIfExists('categorias');
    }
}
