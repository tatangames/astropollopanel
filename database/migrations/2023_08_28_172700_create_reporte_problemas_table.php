<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReporteProblemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporte_problemas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_cliente')->unsigned();

            $table->string('manufactura', 500)->nullable();
            $table->string('nombre', 500)->nullable();
            $table->string('modelo', 500)->nullable();
            $table->string('codenombre', 500)->nullable();
            $table->string('devicenombre', 500)->nullable();

            $table->text('problema');
            $table->dateTime('fecha');

            $table->foreign('id_cliente')->references('id')->on('clientes');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reporte_problemas');
    }
}
