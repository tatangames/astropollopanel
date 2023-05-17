<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);

            // unicamente para situar el mapa la ubicacion donde esta
            $table->string('latitud', 50);
            $table->string('longitud', 50);

            // si hay problemas en esta zona de envio
            $table->boolean('saturacion')->default(0);

            $table->string('mensaje_bloqueo', 100)->nullable();

            // hora abre esta zona y cierra
            $table->time('hora_abierto_delivery');
            $table->time('hora_cerrado_delivery');

            $table->boolean('activo');

            // aumenta el tiempo de una orden, a esta zona
            $table->integer('tiempo_extra');


            // minimo de compra en la zona
            $table->decimal('minimo', 10,2);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas');
    }
}
