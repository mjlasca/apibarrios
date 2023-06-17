<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingDuplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_duplicates', function (Blueprint $table) {
            $table->id();
            $table->string('idpropuesta', 150);
            $table->string('prefijo', 50);
            $table->int('meses', 10);
            $table->double('premio');
            $table->double('premio_total');
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
        Schema::dropIfExists('pending_duplicates');
    }
}
