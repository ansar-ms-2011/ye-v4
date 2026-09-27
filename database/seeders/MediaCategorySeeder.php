<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'category_name' => 'Photo-Self',
                'applicable_on' => 'both'
            ],
            [
                'category_name' => 'Passport-Scan',
                'applicable_on' => 'both'
            ],
            [
                'category_name' => 'Photo-Family',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Photo-Home',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Photo-Interest',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Photo-Important',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Applicant-Letter-Page-1',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Applicant-Letter-Page-2',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Applicant-Letter-Page-3',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Parent-Letter-Page-1',
                'applicable_on' => 'step'
            ],
            [
                'category_name' => 'Parent-Letter-Page-2',
                'applicable_on' => 'step'
            ]
        ];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('media_categories')->truncate();
        foreach($categories as $category){
            DB::table('media_categories')->insert($category);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
