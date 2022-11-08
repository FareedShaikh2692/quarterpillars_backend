<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\BusinessRegistration;
use App\Models\BusinessAccountDetail;
use App\Models\InfluencerRegistration;
use App\Models\Privilages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;
class AdminController extends CommonUtility
{
    private $Mahareferid = '';
 
    public function all_privilages(){
        try {
            $privilages=Privilages::all();
            return $this->get_response(true, 'sucess', ["privilages" => $privilages], 200);
            
        }catch(Exception $e){
            return $this->get_response(true, 'Failed to create', ["msg" => $e], 200);
        }
    }
    public function add_privilages(Request $request){
        $rules = [
            'role_id' => 'required',
            'privilages_name' => 'required',
            'active' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                Privilages::create($request->all());
                $privilages=Privilages::all();
                return $this->get_response(true, 'sucess', ["privilages" => $privilages], 200);
            }
        }catch(Exception $e){
            return $this->get_response(true, 'Failed to create', ["msg" => $e], 200);
        }
    }

    public function update_privilages(Request $request){
        $rules = [
            'role_id' => 'required',
            'privilages_name' => 'required',
            'active' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                Privilages::create($request->all());
            }
        }catch(Exception $e){
            return $this->get_response(true, 'Failed to create', ["msg" => $e], 200);
        }
    }
    public function delete_privilages(Request $request){
        $rules = [
            'role_id' => 'required',
            'privilages_name' => 'required',
            'active' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                Privilages::create($request->all());
            }
        }catch(Exception $e){
            return $this->get_response(true, 'Failed to create', ["msg" => $e], 200);
        }
    }
}
