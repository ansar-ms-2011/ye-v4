<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('media_category_label')->nullable();
            $table->string('brief_caption')->nullable();
            $table->string('file_name')->nullable();
            $table->foreignId('media_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['media_category_label', 'brief_caption', 'file_name']);
            $table->dropConstrainedForeignId('media_category_id');
            $table->dropConstrainedForeignId('application_id');
        });
    }
}
