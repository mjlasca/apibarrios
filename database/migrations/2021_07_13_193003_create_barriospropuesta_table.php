<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarriospropuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrios_propuestas', function (Blueprint $table) {
            $table->id();
            $table->double('reg');
            $table->double('id_propuesta');
            $table->string('id_barrio',100)->nullable();
            $table->string('nombre',2000)->nullable();
            $table->dateTime('ultmod');
            $table->string('user_edit',300)->nullable();
            $table->string('codestado',10)->nullable();
            $table->string('prefijo',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barrios_propuestas');
    }
}
