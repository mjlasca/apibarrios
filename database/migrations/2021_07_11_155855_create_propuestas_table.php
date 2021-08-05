<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propuestas', function (Blueprint $table) {
            $table->id();
            $table->string('reg',200);
            $table->string('documento',100)->nullable();
            $table->text('nombre')->nullable();
            $table->string('num_polizas',5)->nullable();
            $table->string('meses',2)->nullable();
            $table->string('id_cobertura',100)->nullable();
            $table->string('id_barrio',100)->nullable();
            $table->string('nueva_poliza',2)->nullable();
            $table->double('premio')->nullable();
            $table->double('premio_total')->nullable();
            $table->dateTime('fechaDesde')->nullable();
            $table->dateTime('fechaHasta')->nullable();
            $table->string('clausula',2)->nullable();
            $table->string('barrio_beneficiario',2)->nullable();
            $table->dateTime('ultmod')->nullable();
            $table->string('useredit',150)->nullable();
            $table->string('codestado',2)->nullable();
            $table->double('cobertura_suma')->nullable();
            $table->double('cobertura_deducible')->nullable();
            $table->double('cobertura_gastos')->nullable();
            $table->string('promocion',100)->nullable();
            $table->string('paga',1)->nullable();
            $table->dateTime('fecha_paga')->nullable();
            $table->string('referencia',100)->nullable();
            $table->double('prima')->nullable();
            $table->string('master',150)->nullable();
            $table->string('organizador',150)->nullable();
            $table->string('productor',150)->nullable();
            $table->string('puntodeventa',150)->nullable();
            $table->string('prefijo',10)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propuestas');
    }
}
