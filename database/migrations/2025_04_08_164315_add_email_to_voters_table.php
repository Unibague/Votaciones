<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToVotersTable extends Migration
{
    /**
     * Run the migrations.
     * Add the 'email' column to the 'voters' table.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->string('email')->nullable()->after('identification_number');
        });
    }

    /**
     * Reverse the migrations.
     * Remove the 'email' column from the 'voters' table.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
