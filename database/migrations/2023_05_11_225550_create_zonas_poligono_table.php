<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonasPoligonoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas_poligono', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_zonas')->unsigned();
            $table->string('latitud', 50);
            $table->string('longitud', 50);

            $table->foreign('id_zonas')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas_poligono');
    }
}
