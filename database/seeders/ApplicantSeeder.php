<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'applicant')->first();
        if($role){
            $applications = DB::table('applications')->get(['id', 'application_no','email_address', 'firstname']);
            foreach ($applications as $application){
                $user = User::create([
                    'full_name'=>$application->firstname,
                    'email'=>$application->id.'_'.$application->email_address,
                    'password'=>Hash::make('applicant'),
                    'application_id'=>$application->id,
                    'active'=>1,
                ]);
                $user->assignRole($role);
            }
        }
    }
}
