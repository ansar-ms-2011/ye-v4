<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'full_name' => 'Ansar Mehmood',
            'email' => 'ansar.mcs2009@gmail.com',
            'password' => Hash::make('12345678'),
            'district' => null,
            'rotary_club_id' => null,
            'active' => 1,
        ];
        $user = User::create($data);
        $user->assignRole('admin');
    }
}
