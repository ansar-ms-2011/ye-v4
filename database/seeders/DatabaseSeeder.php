<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            DistrictSeeder::class,
            RibiClubsSeeder::class,
            RibiCyeoSeeder::class,
            DyeoSeeder::class,
            AppLanguageSeeder::class,
            AppLanguageSeeder::class,
            UpdateApplicationData::class,
            MenuSeeder::class,
        ]);
    }
}
