<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRibiDyeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ribi_dyeo', function (Blueprint $table) {
            $table->id();
            $table->string('district_code', 10);
            $table->string('dyeo_name', 100)->nullable();
            $table->string('dyeo_email', 45)->nullable();
            $table->string('dyeo_contact_no', 45)->nullable();
            $table->string('dyeo_address', 100)->nullable();
            $table->string('dyeo_city', 100)->nullable();
            $table->string('dyeo_state', 100)->nullable();
            $table->string('dyeo_postcode', 45)->nullable();
            $table->string('dyeo_country', 45)->nullable();
            $table->string('dyeo_htel', 45)->nullable();
            $table->string('dyeo_wtel', 45)->nullable();
            $table->string('dyeo_mobile', 45)->nullable();
            $table->string('dyeo_fax', 45)->nullable();
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
        Schema::dropIfExists('ribi_dyeo');
    }
}
