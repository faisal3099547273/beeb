<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvmodelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csvmodels', function (Blueprint $table) {
            $table->id();
            $table->string('Year');
            $table->string('Make');
            $table->string('Model');
            $table->string('Platform');
            $table->string('Class');
            $table->string('Notes');
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
        Schema::dropIfExists('csvmodels');
    }
}
