<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categories = [
            [
                'category_name'=>'TRAVEL',
            ],
            [
                'category_name'=>'FASHION',
            ],
            [
                'category_name'=>'LIFESTYLE',
            ],
            [
                'category_name'=>'FOOD',
            ]
        ];
        for($i=0; $i<count($categories); $i++){
            DB::table('categories')->insert($categories[$i]);
        }
    }
}