<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('identification_number');

            $table->smallInteger('faculty_code')->nullable();
            $table->foreign('faculty_code')->references('code')->on('faculties');

            $table->smallInteger('program_code')->nullable();
            $table->foreign('program_code')->references('code')->on('programs');

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
        Schema::dropIfExists('voters');
    }
}
