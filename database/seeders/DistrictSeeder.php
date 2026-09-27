<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            '1010',
            '1020',
            '1030',
            '1040',
            '1060',
            '1070',
            '1080',
            '1090',
            '1100',
            '1110',
            '1120',
            '1130',
            '1145',
            '1150',
            '1160',
            '1175',
            '1180',
            '1190',
            '1200',
            '1210',
            '1220',
            '1230',
            '1240',
            '1260',
            '1270',
            '1285',
        ];
        foreach ($list as $item){
            DB::table('districts')->insert(['code'=> $item]);
        }
    }
}
