<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesPremioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_premio', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_ordenes')->unsigned();
            $table->bigInteger('id_cliente')->unsigned();

            // nombre del premio
            $table->string('nombre', 150);

            // costo del premio
            $table->integer('puntos');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_premio');
    }
}
