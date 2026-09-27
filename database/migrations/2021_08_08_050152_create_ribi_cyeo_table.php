<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRibiCyeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ribi_cyeo', function (Blueprint $table) {
            $table->id();
            $table->string('cyeo_name', 100)->nullable();
            $table->string('cyeo_sig')->nullable();             //Blob
            $table->string('cyeo_address', 200)->nullable();
            $table->string('cyeo_city', 100)->nullable();
            $table->string('cyeo_state', 100)->nullable();
            $table->string('cyeo_postcode', 45)->nullable();
            $table->string('cyeo_country', 45)->nullable();
            $table->string('cyeo_htel', 100)->nullable();
            $table->string('cyeo_wtel', 100)->nullable();
            $table->string('cyeo_mobile', 100)->nullable();
            $table->string('cyeo_fax', 100)->nullable();
            $table->string('cyeo_email', 40)->nullable();
            $table->unsignedInteger('application_no')->nullable();
            $table->unsignedBigInteger('ribi_club_id')->nullable();
            $table->timestamps();

            $table->foreign('ribi_club_id')->references('id')->on('ribi_clubs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ribi_cyeo');
    }
}
