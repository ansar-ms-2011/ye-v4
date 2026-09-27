<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationAddress;
use App\Models\Application;
use App\Models\ApplicationLanguage;
use Illuminate\Support\Facades\Log;

class UpdateApplicationData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $applications = Application::all();
        foreach ($applications as $application){

            $addresses = ApplicationAddress::where('application_no', $application->application_no)->get();
            foreach ($addresses as $address){
                $address->application_id = $application->id;
                $address->save();
            }

            $languages = ApplicationLanguage::where('application_no', $application->application_no)->get();
            foreach ($languages as $language){
                $language->application_id = $application->id;
                $language->save();
            }
        }
    }
}
