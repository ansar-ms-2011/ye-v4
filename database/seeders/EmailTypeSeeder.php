<?php

namespace Database\Seeders;

use App\Models\EmailType;
use Illuminate\Database\Seeder;

class EmailTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailType::create(['email_type_label' => 'Guide-Camps']);
        EmailType::create(['email_type_label' => 'Guide-STEP']);
    }
}
