<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('application_no');
            $table->string('language', 50);
            $table->unsignedInteger('years_studied');
            $table->string('speaking', 10);
            $table->string('reading', 10);
            $table->string('writing', 10);
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
        Schema::dropIfExists('application_languages');
    }
}
