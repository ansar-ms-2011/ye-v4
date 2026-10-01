<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE TABLE media (id INTEGER PRIMARY KEY AUTOINCREMENT, media BLOB, created_at TIMESTAMP)');
        } else {
            DB::statement('CREATE TABLE media (id INTEGER PRIMARY KEY AUTO_INCREMENT, media MEDIUMBLOB, created_at TIMESTAMP)');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
