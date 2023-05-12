<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_servicio', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_servicios')->unsigned();
            $table->time('hora1');
            $table->time('hora2');
            $table->integer('dia');

            $table->boolean('cerrado')->default(0);

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
        Schema::dropIfExists('horario_servicio');
    }
}
