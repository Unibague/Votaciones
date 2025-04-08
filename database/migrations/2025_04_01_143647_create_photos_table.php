<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete(); // Relación con candidatos
            $table->string('path'); // Ruta de la imagen
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('photos');
    }
}