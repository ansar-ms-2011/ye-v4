<?php

namespace Database\Seeders;

use App\Models\RibiCyeo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CyeoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cyeos = RibiCyeo::whereNotNull('cyeo_name')->whereNotNull('cyeo_email')->get();
        $role = Role::where('name', 'cyeo')->first();
        foreach ($cyeos as $cyeo) {
            if ($cyeo->user_id) {
                DB::table('model_has_roles')->where('model_id', $cyeo->user_id)->delete();
                User::destroy($cyeo->user_id);
            }
            $user = User::where('email', $cyeo->cyeo_email)->first();
            if ($user) {
                $cyeo->user_id = $user->id;
                $cyeo->save();
            } else {
                $user = User::create([
                    'full_name' => $cyeo->cyeo_name ?: 'no name',
                    'email' => $cyeo->cyeo_email,
                    'active' => 1,
                    'rotary_club_id' => $cyeo->ribi_club_id,
                    'password' => Hash::make('12345678'),
                ]);
                $cyeo->user_id = $user->id;
                $cyeo->save();
                $user->assignRole($role);
            }
        }
    }
}
