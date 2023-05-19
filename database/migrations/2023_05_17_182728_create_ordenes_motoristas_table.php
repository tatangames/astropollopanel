<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesMotoristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_motoristas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_ordenes')->unsigned();
            $table->bigInteger('id_motorista')->unsigned();

            // FECHA QUE AGARRO LA ORDEN
            $table->dateTime('fecha');

            $table->integer('experiencia')->nullable();
            // nota del cliente
            $table->string('mensaje', 200)->nullable();


            $table->foreign('id_ordenes')->references('id')->on('ordenes');
            $table->foreign('id_motorista')->references('id')->on('motoristas_servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_motoristas');
    }
}
