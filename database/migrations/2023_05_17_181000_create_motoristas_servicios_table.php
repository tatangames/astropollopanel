<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotoristasServiciosTable extends Migration
{
    /**
     * VARIOS MOTORISTAS POR CADA RESTAURANTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motoristas_servicios', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_servicios')->unsigned();

            $table->string('usuario', 20)->unique();
            $table->string('password', 255);

            // token para enviar notificaciones
            $table->string('token_fcm', 100)->nullable();

            $table->string('nombre', 100);

            $table->string('vehiculo', 50);
            $table->string('placa', 50);

            // PARA RECIBIR O NO NOTIFICACIONES
            $table->boolean('notificacion');

            $table->boolean('activo');

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
        Schema::dropIfExists('motoristas_servicios');
    }
}
