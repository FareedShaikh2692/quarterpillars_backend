<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRole;

class UserController extends Controller
{
    public function get_all_user_type(Request $request){
        $users = UserRole::all();
        if(count($users)>0){
            return ['error'=>null, 'status'=>200, 'msg'=>'Users found', 'users'=>$users];
        }
        return ['error'=>null, 'status'=>403, 'msg'=>'No users found', 'users'=>[]];
    }
}
