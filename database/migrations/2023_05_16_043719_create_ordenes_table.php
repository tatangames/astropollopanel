<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesTable extends Migration
{
    /**
     * ORDENES
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();


            $table->bigInteger('id_cliente')->unsigned();
            $table->bigInteger('id_servicio')->unsigned();

            $table->string('nota_orden', 600)->nullable();

            // precio sin afectar cupones
            $table->decimal('total_orden', 10,2);

            $table->dateTime('fecha_orden');


            // FECHA ESTIMADA DE ENTREGA PARA EL CLIENTE + SUMA DE ZONA
            $table->dateTime('fecha_estimada')->nullable();



            // 0: NO INICIADA   1: ORDEN INICIADA
            $table->boolean('estado_iniciada');
            $table->dateTime('fecha_iniciada')->nullable();

            // 0: NO PREPARADA   1: YA PREPARADA

            $table->boolean('estado_preparada')->default(0);
            $table->dateTime('fecha_preparada')->nullable();

            // 0: ORDEN NO ENCAMINO   1: ORDEN YA EN CAMINO

            $table->boolean('estado_camino')->default(0);
            $table->dateTime('fecha_camino')->nullable();

            // 0: ORDEN NO FINALIZA POR MOTORISTA  1: ORDEN FINALIZADA POR MOTORISTA

            $table->boolean('estado_entregada')->default(0);
            $table->dateTime('fecha_entregada')->nullable();

            // 0: NO CANCELADA   1: ORDEN CANCELADA

            $table->boolean('estado_cancelada')->default(0);
            $table->dateTime('fecha_cancelada')->nullable();
            $table->string('nota_cancelada', 300)->nullable();



            // DATOS DE CUPONES
            $table->bigInteger('id_cupones')->unsigned()->nullable();

            // lo que pagara cliente si aplico cupon dinero o porcentaje
            $table->decimal('total_cupon')->nullable();
            // describiendo lo que se aplico
            $table->string('mensaje_cupon', 400)->nullable();



            // LA ORDEN ES VISIBLE POR EL CLIENTE EN EL LISTADO DE ORDENES ACTIVAS
            $table->boolean('visible');





            $table->boolean('visible_p');
            $table->boolean('visible_p2');
            $table->boolean('visible_p3');

            // 0: CANCELADA POR CLIENTE    1: CANCELADA POR RESTAURANTE
            $table->boolean('cancelado_por');

            $table->boolean('visible_m');





            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_servicio')->references('id')->on('servicios');
            $table->foreign('id_cupones')->references('id')->on('cupones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
}
