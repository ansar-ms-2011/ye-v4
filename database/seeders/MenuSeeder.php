<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu_data = [
            ['label' => 'Users', 'path' => '/user', 'icon' => 'mdi-account-group', 'role_names' => 'admin'],
            ['label' => 'Clubs', 'path' => '/club', 'icon' => 'mdi-image', 'role_names' => 'admin,dyeo'],
            ['label' => 'CYEOs', 'path' => '/cyeo', 'icon' => 'mdi-account-supervisor', 'role_names' => 'admin,dyeo'],
            ['label' => 'DYEOs', 'path' => '/dyeo', 'icon' => 'mdi-account-tie', 'role_names' => 'admin'],
            ['label' => 'My Profile', 'path' => '/profile', 'icon' => 'mdi-account', 'role_names' => 'dyeo,cyeo'],
            ['label' => 'Applications', 'path' => '/application', 'icon' => 'mdi-card-account-details-outline', 'role_names' => 'admin,dyeo,cyeo'],
        ];

        DB::table('menu')->truncate();
        foreach ($menu_data as $menu_datum) {
            DB::table('menu')->insert($menu_datum);
        }
    }
}
