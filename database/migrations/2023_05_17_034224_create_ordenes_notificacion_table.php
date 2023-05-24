<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesNotificacionTable extends Migration
{
    /**
     * ORDENES QUE ESTAN AQUI SE ENVIARA NOTIFICACION A LOS USUARIOS DE RESTAURANTE
     * SOLO ABRA 1 USUARIO, ESTO ES PARA QUE CONTESTEN LA ORDEN
     * SE ENVIARA NOTI CADA 1 MINUTO, HASTA QUE SEA INICIADA O CANCELDAD
     * SE BORRA EL REGISTRO AQUI
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_notificacion', function (Blueprint $table) {
            $table->id();

            // con esto saco el usuario asignado al restaurante
            $table->bigInteger('id_ordenes')->unsigned();

            $table->foreign('id_ordenes')->references('id')->on('ordenes');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_notificacion');
    }
}
