<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresidentEmailMobileToClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ribi_clubs', function (Blueprint $table) {
            $table->string('club_president_email')->nullable()->after('club_president');
            $table->string('club_president_mobile')->nullable()->after('club_president_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ribi_clubs', function (Blueprint $table) {
            $table->dropColumn('club_president_email');
            $table->dropColumn('club_president_mobile');
        });
    }
}
