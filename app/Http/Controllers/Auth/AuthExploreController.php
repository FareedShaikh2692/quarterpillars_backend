<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\QpUserCart;
use App\Models\QPAddress;
use App\Models\BusinessRegistration;
use App\Models\BusinessAccountDetail;
use App\Models\ExploerRegistration;
use App\Models\BusinessProducts;
use App\Models\QpUserOrders;
use App\Models\Transection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;
use Easebuzz;



class AuthExploreController extends CommonUtility
{
    private $Mahareferid = '';

    public function explore_registration(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'mobile_number' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'address_type' => 'required',
            'location' => 'required',
            'land_mark' => 'required',
            'address_1' => 'required',
            'address_2' => 'required',
            'pin_code' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'password' => 'required',
            'is_email_verified' => 'required',
            'is_mobile_verified' => 'required',
            'profile_avatar' => 'required',
        ];
       

        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                if (User::create(['role_id' => 1, 'name' => $request->username, 'email' => $request->email, 'mobile_number' => $request->mobile_number, 'password' => Hash::make($request->password)])) {
                    $user = User::where('name', $request->username)->first();
                    $file = $request->profile_avatar;
                    $exte = $file->extension();
                    $img_name = substr(md5(mt_rand()), 0, 7);
                    $path = 'profile/' . $user->id . '/' . $img_name . "." . $exte;
                    $file->move(public_path('profile/' . $user->id), $img_name . "." . $exte);
                    $request->merge(['avatar' => $path]);
                    $request->merge(['explore_id' => $user->id]);
                    $user_address = [
                        'user_id' => $user->id,
                        'address_name' => $request->address_1,
                        'address_type' => $request->address_type,
                        'zip_code' => $request->pin_code,
                        'city' => $request->city,
                        'state' =>  $request->state,
                        'address' => $request->location,
                        'landmark' => $request->land_mark,
                    ];
                    ExploerRegistration::create($request->all());
                    QPAddress::create($user_address);
                    $user_details = User::where('id', $user->id)->with('explore')->first();
                    //$token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    return $this->get_response(false, 'User created successfully', ['registration_success' => true,  'user_details' => $user_details], 200);
                }
                return $this->get_response(true, 'Failed to creaste user', null, 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to creaste user', ["msg" => $e], 200);
        }
    }

    public function edit_explore_profile(Request $request)
    {
        try {
            if ($request->avatar) {
                $file = $request->avatar;
                $exte = $file->extension();
                $img_name = substr(md5(mt_rand()), 0, 7);
                $path = 'profile/' . $request->explore_id . '/' . $img_name . "." . $exte;
                $file->move(public_path('profile/' . $request->explore_id), $img_name . "." . $exte);
                $request->merge(['pro_avatar' => $path]);
            }
            $check = User::where('id', $request->explore_id)->with('explore')->first();

            User::where('id', $request->explore_id)
                ->update([
                    'name' => $request->username,
                ]);
            ExploerRegistration::where('explore_id', $request->explore_id)
                ->update([
                    'name'               => $request->name,
                    'username'           => $request->username,
                    'dob'                => $request->dob,
                    'gender'             => $request->gender,
                    'avatar'             => $request->pro_avatar,
                ]);
            $user = User::where('id', $request->explore_id)->with('explore')->first();
            $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
            return $this->get_response(true, 'updated successfully', ['updated_success' => true, 'token' => $token, 'user_details' => $user], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }






}
