<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);

            // numero del local
            $table->string('telefono', 20);

            // minimo de compra en el local
            $table->decimal('minimo', 10,2);
            $table->boolean('utiliza_minimo');

            // tiempo predeterminado para contestar una orden
            $table->integer('tiempo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
