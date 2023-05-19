<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSliderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_producto')->unsigned()->nullable();
            $table->bigInteger('id_servicios')->unsigned();

            $table->string('nombre', 200)->nullable();

            $table->string('imagen', 100);
            $table->integer('posicion');

            // redireccionamiento en app cliente al tocar slider
            $table->boolean('redireccionamiento');

            // utiliza horarios
            $table->boolean('usa_horario');
            $table->time('hora_abre');
            $table->time('hora_cierra');

            $table->boolean('activo');

            $table->foreign('id_producto')->references('id')->on('productos');
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
        Schema::dropIfExists('slider');
    }
}
