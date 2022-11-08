<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_roles = [
            [
                'role_name'=>'Admin',
                'bg_video'=>'',
                'role_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.'
            ],
            [
                'role_name'=>'Influencer',
                'bg_video'=>'http://qp.flymingotech.in/public/videos/inf.mp4',
                'role_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.'
            ],
            [
                'role_name'=>'Explorer',
                'bg_video'=>'http://qp.flymingotech.in/public/videos/explore.mp4',
                'role_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.'
            ],
            [
                'role_name'=>'Business',
                'bg_video'=>'http://qp.flymingotech.in/public/videos/business.mp4',
                'role_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.'
            ],
            [
                'role_name'=>'Advertiser',
                'bg_video'=>'http://qp.flymingotech.in/public/videos/adv.mp4',
                'role_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.'
            ],
        ];
        for($i=0; $i<count($user_roles); $i++){
            DB::table('user_roles')->insert($user_roles[$i]);
        }
    }
}
