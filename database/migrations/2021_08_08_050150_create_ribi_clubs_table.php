<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRibiClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ribi_clubs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('club_name');
            $table->string('club_president')->nullable();
            $table->string('club_president_sig')->nullable();
            $table->string('club_other_name')->nullable();
            $table->string('club_other_sig')->nullable();
            $table->unsignedInteger('district_code')->nullable();
            $table->timestamps();

            $table->foreignId('district_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ribi_clubs');
    }
}
