<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\BusinessRegistration;
use App\Models\BusinessAccountDetail;
use App\Models\AadhaarVerification;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\BusinessCollabration;
use App\Models\InfluencerRegistration;
use Exception;

class AuthBusinessController extends CommonUtility
{
    //
    private $Mahareferid = '';


    public function business_registration(Request $request)
    {

        $rules = [

            'catorige' => 'required',
            'sub_catorige' => 'required',
            'company_name' => 'required',
            'owner_name' => 'required',
            'business_email' => 'required',
            'owner_email' => 'required',
            'owner_phone' => 'required',
            'owner_adhar' => 'required',
            'business_doc_id' => 'required',
            'profile_avatar' => 'required',
            'business_password' => 'required',
            'business_address' => 'required',
            'username' => 'required',
            'is_product' => 'required',
            'is_service' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'is_biz_email_verified' => 'required',
            'is_owner_email_verified' => 'required',
            'is_phone_no_verified' => 'required',
            'is_adhar_verifed' => 'required',
            'is_business_doc_verfied' => 'required',
            'is_gst_verfied' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);


        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 200);
        } else {
            if (User::create(['role_id' => 4, 'name' => $request->username, 'email' => $request->owner_email, 'mobile_number' => $request->owner_phone, 'password' => Hash::make($request->business_password)])) {
                $user = User::where('name', $request->username)->first();
                $file = $request->profile_avatar;
                $exte = $file->extension();
                $img_name = substr(md5(mt_rand()), 0, 7);
                $path = 'profile/' . $user->id . '/' . $img_name . "." . $exte;
                $file->move(public_path('profile/' . $user->id), $img_name . "." . $exte);
                $request->merge(['business_id' => $user->id]);
                $request->merge(['business_profile_pic' => $path]);
                BusinessRegistration::create($request->all());
                $user_details = User::where('id', $user->id)->with('business')->first();
                $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                return $this->get_response(false, 'User created successfully', ['registration_success' => true, 'token' => $token, 'user_details' => $user_details], 200);
            }
            return $this->get_response(true, 'Failed to creaste user', null, 200);
        }
    }

    public function edit_bussiness_profile(Request $request)
    {
        try {
            if ($request->profile_avatar) {
                $file = $request->profile_avatar;
                $exte = $file->extension();
                $img_name = substr(md5(mt_rand()), 0, 7);
                $path = 'profile/' . $request->business_id . '/' . $img_name . "." . $exte;
                $file->move(public_path('profile/' . $request->business_id), $img_name . "." . $exte);
                $request->merge(['business_profile_pic' => $path]);
            }
            $check = User::where('id', $request->business_id)->with('business')->first();

            User::where('id', $request->business_id)
                ->update([
                    'name'          => $request->username,
                ]);

            BusinessRegistration::where('business_id', $request->business_id)
                ->update([
                    'sub_catorige'          => $request->sub_catorige,
                    'business_profile_pic'  => $request->business_profile_pic,
                    'username'              => $request->username,
                ]);
            $user = User::where('id', $request->business_id)->with('business')->first();
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
    public function aadhar_number(Request $request)
    {
        $rules = [
            'aadhaar_number' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            if ($response = $this->aadhaar_otp($request->aadhaar_number)) {
                $data = json_decode($response, true);
                if ($data) {
                    if (AadhaarVerification::where(['aadhaar_number' => $request->aadhaar_number])->first()) {
                    } else {
                        AadhaarVerification::create($request->all());
                    }
                    $this->Mahareferid = 0;
                    return $this->get_response(false, 'Otp sent successfully', ['Mahareferid' => $data['Data']], 200);
                } else {
                    return $this->get_response(true, 'Otp faild to send', ['otp_send' => false], 200);
                }
            }
            return $this->get_response(true, 'Otp not sent', ['otp_send' => false], 200);
        }
    }
    public function aadhar_verification(Request $request)
    {
        $rules = [
            'otp' => 'required',
        ];


        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {

            $response = $this->aadhaar_verification($request->otp, $request->Mahareferid);
            return $data = json_decode($response, true);
            if (count($data['Data'])) {
                $user = User::where(['mobile_number' => $request->mobile_number])->first();

                if ($user) {
                    AadhaarVerification::where(['business_id' => $request->business_id])->update(['is_aadhaar_verified' => 1]);
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    $banDetails = BusinessAccountDetail::where(['business_id' => $user->id])->first();
                    return $this->get_response(false, 'User Found', ['token' => $token, 'has_bank_details' => $banDetails ? $banDetails->id : false], 200);
                }

                return $this->get_response(false, 'User not Found', ['token' => ''], 200);
            } else {
                return $this->get_response(true, 'OTP did not matched', null, 200);
            }
        }
    }
    public function add_business_bank_details(Request $request)
    {
        $rules = [
            'gst_number' => 'required',
            'pan_number' => 'required',
            'bank_account_number' => 'required',
            'bank_ifsc_code' => 'required',
            'bank_branch' => 'required',
            'product_or_service_details' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            if (BusinessAccountDetail::create($request->all())) {
                return $this->get_response(false, 'Bank details added succewssfully', null, 200);
            } else {
                return $this->get_response(true, 'Failed to add bank account details', null, 400);
            }
        }
    }

    public function get_all_business()
    {
        try {
            $Business_details = BusinessRegistration::all();
            return $this->get_response(true, 'successfully', ['Request' => true, 'Business_details' => $Business_details], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }

    public function add_bussiness_sub_user(Request $request)
    {
    }

    public function get_bussiness_sub_user(Request $request)
    {
    }

    public function req_business_collobration(Request $request)
    {
        $rules = [
            'business_id' => 'required',
            'influencer_id' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                $request->merge(['status' => 'pending']);
                BusinessCollabration::create($request->all());
                $influencer = User::where('id', $request->influencer_id)->with('influencer')->first();
                return $this->get_response(true, 'Request successfully', ['Request' => true, 'influencer_details' => $influencer], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }

    public function request_business_collobration($id)
    {
        try {
            $array_influencer = [];
            $BusinessCollabration = BusinessCollabration::where('business_id', $id)->get();
            foreach ($BusinessCollabration as $item) {
                $influencer = InfluencerRegistration::where('influencer_id', $item->influencer_id)->first();
                $influencer['collabration_status']= $item->status;
                $influencer['collabration_id'] = $item->id;
                $influencer['collabration_created_at']=$item->created_at;
                array_push($array_influencer,$influencer);
            }
            return $this->get_response(true, 'successfully', ['Request' => true, 'influencer' => $array_influencer], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }
}
