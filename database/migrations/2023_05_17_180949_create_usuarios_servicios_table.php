<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosServiciosTable extends Migration
{
    /**
     * 1 USUARIO POR CADA RESTAURANTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_servicios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_servicios')->unsigned();

            $table->string('usuario', 20)->unique();
            $table->string('password', 255);

            // token para enviar notificaciones
            $table->string('token_fcm', 100)->nullable();

            $table->boolean('bloqueado');

            // nombre de la persona
            $table->string('nombre', 100);

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
        Schema::dropIfExists('usuarios_servicios');
    }
}
