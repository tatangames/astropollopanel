<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonasServicioTable extends Migration
{
    /**
     * Asignar cada servicio a una Zona, ya que pueden haber varias zonas para 1 solo servicio
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas_servicio', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_zonas')->unsigned();
            $table->bigInteger('id_servicios')->unsigned();


            // para ocultar boton borrar registro por 15 minutos
            $table->dateTime('fecha');


            $table->foreign('id_zonas')->references('id')->on('zonas');
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
        Schema::dropIfExists('zonas_servicio');
    }
}
