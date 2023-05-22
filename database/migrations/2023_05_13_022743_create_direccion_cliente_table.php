<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direccion_cliente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_zonas')->unsigned();
            $table->bigInteger('id_cliente')->unsigned();

            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('punto_referencia', 400)->nullable();
            $table->boolean('seleccionado');
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();
            $table->string('telefono', 10);

            // puntos donde se registro la direccion
            $table->string('latitudreal', 50)->nullable();
            $table->string('longitudreal', 50)->nullable();

            $table->foreign('id_zonas')->references('id')->on('zonas');
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
        Schema::dropIfExists('direccion_cliente');
    }
}
