<?php

namespace Database\Seeders;

use App\Models\RibiDyeo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DyeoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dyeos = RibiDyeo::whereNotNull('dyeo_name')->whereNotNull('dyeo_email')->get();
        $role = Role::where('name', 'dyeo')->first();
        foreach ($dyeos as $dyeo) {
            if ($dyeo->user_id) {
                DB::table('model_has_roles')->where('model_id', $dyeo->user_id)->delete();
                User::destroy($dyeo->user_id);
            }
            $user = User::where('email', $dyeo->dyeo_email)->first();
            if ($user) {
                $dyeo->user_id = $user->id;
                $dyeo->save();
            } else {
                $user = User::create([
                    'full_name' => $dyeo->dyeo_name ?: 'no name',
                    'email' => $dyeo->dyeo_email,
                    'district' => $dyeo->district_code,
                    'active' => 1,
                    'rotary_club_id' => $dyeo->ribi_club_id,
                    'password' => Hash::make('12345678'),
                ]);
                $dyeo->user_id = $user->id;
                $dyeo->save();
                $user->assignRole($role);
            }
        }
    }
}
