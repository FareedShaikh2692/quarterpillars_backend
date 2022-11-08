<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\ExploerRegistration;
use App\Models\BusinessAccountDetail;
use App\Models\AdvertiserRegistration;
use App\Models\AadhaarVerification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Exception;

class AuthAdvertiserController extends CommonUtility
{
    //
    private $Mahareferid = '';

    public function advertiser_registration(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'profile_avatar' => 'required',
            'mobile_number' => 'required',
            'gst' => 'required',
            'pan_card' => 'required',
            'company_name' => 'required',
            'company_website' => 'required',
            'company_address' => 'required',
            'campany_Owner_name' => 'required',
            'is_email_verified' => 'required',
            'is_mobile_verified' => 'required',
            'is_gst_verified' => 'required',
            'is_pan_card_verified' => 'required',
            'is_company_verified' => 'required',
            'password' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                if (User::create(['role_id' => 3, 'name' => $request->username, 'email' => $request->email, 'mobile_number' => $request->mobile_number, 'password' => Hash::make($request->password)])) {
                    $user = User::where('name', $request->username)->first();
                    $file = $request->profile_avatar;
                    $exte = $file->extension();
                    $img_name = substr(md5(mt_rand()), 0, 7);
                    $path = 'profile/' . $user->id . '/' . $img_name . "." . $exte;
                    $file->move(public_path('profile/' . $user->id), $img_name . "." . $exte);
                    $request->merge(['advertiser_id' => $user->id]);
                    $request->merge(['avatar' => $path]);
                    AdvertiserRegistration::create($request->all());
                    $user_details = User::where('name', $request->username)->with('advertiser')->first();
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    return $this->get_response(false, 'User created successfully', ['registration_success' => true, 'token' => $token, 'user_details' => $user_details], 200);
                }
                return $this->get_response(true, 'Failed to create user', null, 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to create user', ["msg" => $e], 200);
        }
    }

    public function edit_advertiser_profile(Request $request)
    {
        try {
            if ($request->avatar) {
                $file = $request->avatar;
                $exte = $file->extension();
                $img_name = substr(md5(mt_rand()), 0, 7);
                $path = 'profile/' . $request->advertiser_id . '/' . $img_name . "." . $exte;
                $file->move(public_path('profile/' . $request->advertiser_id), $img_name . "." . $exte);
                $request->merge(['pro_avatar' => $path]);
            }
           
            User::where('id', $request->advertiser_id)
                ->update([
                    'name' => $request->username,
                ]);

            AdvertiserRegistration::where('advertiser_id', $request->advertiser_id)
                ->update([
                    'name'                 => $request->name,
                    'username'             => $request->username,
                    'avatar'               => $request->pro_avatar,
                ]);
            $user = User::where('id', $request->advertiser_id)->with('advertiser')->first();
            $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
            return $this->get_response(true, 'updated successfully', ['updated_success' => true, 'token' => $token, 'user_details' => $user], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }
    public function add_business_account_details(Request $request)
    {
        $rules = [
            'gst_number' => 'required',
            'pan_number' => 'required',
            'bank_account_number' => 'required',
            'bank_account_holder_name' => 'required',
            'bank_ifsc_code' => 'required',
            'bank_branch' => 'required',
            'product_or_service_details' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            if (BusinessAccountDetail::create($request->all())) {
                return $this->get_response(false, 'Bank detail successfully saved', ['saveBankDetails' => true], 200);
            } else {
                return $this->get_response(true, 'Bank detail failed to saved', ['saveBankDetails' => false], 400);
            }
        }
    }
}
