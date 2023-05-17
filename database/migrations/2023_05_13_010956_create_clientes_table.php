<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 20)->unique();
            $table->string('correo', 100)->unique()->nullable();
            $table->string('codigo_correo',10)->nullable();
            $table->string('password', 255);

            // fecha de registro del cliente
            $table->dateTime('fecha');

            // para bloquear usuario
            $table->boolean('activo');

            // token para enviar notificaciones por one signal
            $table->string('token_fcm', 100)->nullable();

            // el cliente decide si borra carrito de compras al realizar una orden
            $table->boolean('borrar_carrito');


            // para saber de que app se registro
            $table->string('appregistro', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
