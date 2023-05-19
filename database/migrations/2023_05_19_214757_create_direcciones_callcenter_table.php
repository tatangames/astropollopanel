<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionesCallcenterTable extends Migration
{
    /**
     * DIRECCIIONES QUE GUARDARA EL CALL CENTER
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direcciones_callcenter', function (Blueprint $table) {
            $table->id();

            //LLEVA ID DEL RESTAURANTE, NO DEL ID ZONA




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direcciones_callcenter');
    }
}
