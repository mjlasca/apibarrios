<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineaspropuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineas_propuestas', function (Blueprint $table) {
            $table->id();
            $table->double('reg');
            $table->double('id_propuesta');
            $table->string('documento',100)->nullable();
            $table->string('tipo_documento',50)->nullable();
            $table->string('apellidos',200)->nullable();
            $table->string('nombres',200)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('id_actividad',10)->nullable();
            $table->string('id_clasificacion',10)->nullable();
            $table->double('premio');
            $table->dateTime('ultmod');
            $table->string('user_edit',150)->nullable();
            $table->string('codestado',2)->nullable();
            $table->string('prefijo',50)->nullable();
            $table->string('actividad',300)->nullable();
            $table->string('clasificacion',300)->nullable();
            $table->dateTime('fechaDesde')->nullable();
            $table->dateTime('fechaHasta')->nullable();
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
        Schema::dropIfExists('lineas_propuestas');
    }
}
