<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\RibiDyeo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UpdateApplicationDyeoIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $applications = Application::where('dyeo_id', '>', 36)->get();
        foreach ($applications as $application) {
            $dyeo = RibiDyeo::where('district_code', $application->dyeo_id)->first();
            if ($dyeo) {
                $application->dyeo_id = $dyeo->id;
                $application->save();
            }
        }
        Log::info('Application Dyeo ID updated');
    }
}
