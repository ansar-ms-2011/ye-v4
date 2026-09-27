<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no');
            $table->string('idapplication')->nullable();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->boolean('emergency_contact')->nullable();
            $table->boolean('parent_div_sep')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('email_address', 100)->nullable();
            $table->string('school', 100)->nullable();
            $table->string('why_rotary', 100)->nullable();
            $table->boolean('consider_alt_country')->nullable();
            $table->string('how_did_you_hear', 100)->nullable();
            $table->string('firstname', 35)->nullable();
            $table->string('surname', 35)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('county', 100)->nullable();
            $table->string('postcode', 100)->nullable();
            $table->string('contact_no', 45)->nullable();
            $table->string('alt_contact_no', 35)->nullable();
            $table->string('parent_1', 35)->nullable();
            $table->string('parent_2', 35)->nullable();
            $table->string('citizen_of', 50)->nullable();
            $table->string('other_info', 35)->nullable();
            $table->string('exchange_type', 35)->nullable();
            $table->string('pref_name', 35)->nullable();
            $table->string('parent_support', 35)->nullable();
            $table->unsignedInteger('dyeo_id')->nullable();      // Review foreign key
            $table->string('realm', 35)->nullable();                            //Review User Role
            $table->string('application_status', 35)->nullable();
            $table->string('application_status_note', 255)->nullable();
            $table->boolean('dyeo_assigned')->nullable();
            $table->decimal('application_fee')->nullable();
            $table->boolean('application_fee_paid')->nullable();
            $table->string('parent1_rotarian', 45)->nullable();
            $table->string('parent2_rotarian', 45)->nullable();
            $table->string('parent1_rotary_club', 50)->nullable();
            $table->string('parent2_rotary_club', 50)->nullable();
            $table->string('parent1_email', 45)->nullable();
            $table->string('parent2_email', 45)->nullable();
            $table->string('parent1_tel', 45)->nullable();
            $table->string('parent2_tel', 45)->nullable();
            $table->string('parent1_mobile', 45)->nullable();
            $table->string('parent2_mobile', 45)->nullable();
            $table->string('parent1_btel', 45)->nullable();
            $table->string('parent2_btel', 45)->nullable();
            $table->string('parent1_occupation', 100)->nullable();
            $table->string('parent2_occupation', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('diet_restriction', 200)->nullable();
            $table->boolean('smoke')->nullable();
            $table->string('smoke_why', 200)->nullable();
            $table->boolean('drink')->nullable();
            $table->string('drink_why', 200)->nullable();
            $table->boolean('illegal_drugs')->nullable();
            $table->string('illegal_drugs_why', 200)->nullable();
            $table->string('native_language', 50)->nullable();
            $table->boolean('dietary_restriction')->nullable();
            $table->boolean('medical_condition')->nullable();
            $table->boolean('treated_condition')->nullable();
            $table->boolean('prescribed_meds')->nullable();
            $table->boolean('special_req')->nullable();
            $table->string('medical_info', 650)->nullable();
            $table->unsignedInteger('rotary_club_id')->nullable() ;    //Review Foreign Key
            $table->string('free_activities', 650)->nullable();
            $table->string('attainment_vocation', 650)->nullable();
            $table->string('special_interests', 650)->nullable();
            $table->string('special_skills', 650)->nullable();
            $table->string('contrib_entertainment', 650)->nullable();
            $table->string('reason_for_camp', 650)->nullable();
            $table->string('personal_remarks', 650)->nullable();
            $table->string('place_of_birth', 200)->nullable();
            $table->string('em_name', 200)->nullable();
            $table->string('em_relationship', 100)->nullable();
            $table->string('em_htel', 45)->nullable();
            $table->string('em_mobile', 45)->nullable();
            $table->string('em_email', 100)->nullable();
            $table->string('country_citizenship', 200)->nullable();
            $table->string('image_location', 150)->nullable();
            $table->datetime('date_of_app')->nullable();
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
        Schema::dropIfExists('applications');
    }
}
