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
use App\Models\BusinessCollabration;
use App\Models\AadhaarVerification;
use App\Models\InfluencerPosts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthInfluencerController extends CommonUtility
{
    private $Mahareferid = '';

    public function influencer_registration(Request $request)
    {
        $rules = [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'mobile_number' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'profile_avatar' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'insta_link' => 'required',
            'insta_follower_count' => 'required',
            'fb_link' => 'required',
            'fb_follower_count' => 'required',
            'youtube_link' => 'required',
            'youtube_subscribers' => 'required',
            'twitter_link' => 'required',
            'twitter_follower_count' => 'required',
            'tiktok_link' => 'required',
            'tiktok_followers_count' => 'required',
            'is_email_verified' => 'required',
            'is_mobile_verified' => 'required',
            'password' => 'required'
        ];
        try {
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                if (User::create(['role_id' => 2, 'name' => $request->username, 'email' => $request->email, 'mobile_number' => $request->mobile_number, 'password' => Hash::make($request->password)])) {
                    $user = User::where('name', $request->username)->first();
                    $file = $request->profile_avatar;
                    $exte = $file->extension();
                    $img_name = substr(md5(mt_rand()), 0, 7);
                    $path = 'profile/' . $user->id . '/' . $img_name . "." . $exte;
                    $file->move(public_path('profile/' . $user->id), $img_name . "." . $exte);
                    $request->merge(['influencer_id' => $user->id]);
                    $request->merge(['avatar' => $path]);
                    InfluencerRegistration::create($request->all());
                    $user_details = User::where('id', $user->id)->with('influencer')->first();
                    //$token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
                    $token = '';
                    return $this->get_response(false, 'User created successfully', ['registration_success' => true, 'token' => $token, 'user_details' => $user_details], 200);
                }
                return $this->get_response(true, 'Failed to creaste user', null, 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to creaste user', ["msg" => $e], 200);
        }
    }

    public function edit_influencer_profile(Request $request)
    {
        try {
            if ($request->avatar) {
                $file = $request->avatar;
                $exte = $file->extension();
                $img_name = substr(md5(mt_rand()), 0, 7);
                $path = 'profile/' . $request->influencer_id . '/' . $img_name . "." . $exte;
                $file->move(public_path('profile/' . $request->influencer_id), $img_name . "." . $exte);
                $request->merge(['pro_avatar' => $path]);
            }
            $check = User::where('id', $request->influencer_id)->with('influencer')->first();

            User::where('id', $request->influencer_id)
                ->update([
                    'name' => $request->username,

                ]);

            InfluencerRegistration::where('influencer_id', $request->influencer_id)
                ->update([
                    'name'                   => $request->name,
                    'username'               => $request->username,
                    'dob'                    => $request->dob,
                    'gender'                 => $request->gender,
                    'avatar'                 => $request->pro_avatar,
                ]);
            $user = User::where('id', $request->influencer_id)->with('influencer')->first();
            $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
            return $this->get_response(true, 'updated successfully', ['updated_success' => true, 'token' => $token, 'user_details' => $user], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }

    public function get_req_influencer_collabration($id)
    {
        try {
            $array_Business = [];
            $BusinessCollabration = BusinessCollabration::where('influencer_id', $id)->get();
            foreach ($BusinessCollabration as $item) {
                $Business = BusinessRegistration::where('business_id', $item->business_id)->first();
                $Business['collabration_status'] = $item->status;
                $Business['collabration_id'] = $item->id;
                array_push($array_Business, $Business);
            }
            return $this->get_response(true, 'successfully', ['Request' => true, 'Business' => $array_Business], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }

    public function get_all_influencers()
    {
        try {
            $influencers =  InfluencerRegistration::all();
            return $this->get_response(true, 'successfully', ['influencers' => true, 'influencers' => $influencers], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get influencers', [$e], 200);
        }
    }
    public function get_all_influencers_by_id($id)
    {
        try {
            $influencers =  InfluencerRegistration::where('influencer_id', $id)->first();
            return $this->get_response(true, 'successfully', ['influencers' => true, 'influencers' => $influencers], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get influencers', [$e], 200);
        }
    }

    public function res_business_collobration(Request $request)
    {
        $rules = [
            'collobration_id' => 'required',
            'status' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                BusinessCollabration::where('id', $request->collobration_id)
                    ->update([
                        'status'                   => $request->status,
                    ]);
                $collobration = BusinessCollabration::find($request->collobration_id);

                $business = BusinessRegistration::where('business_id', $collobration->business_id)->first();

                return $this->get_response(true, 'successfully', ['Request' => true, 'business_details' => $business], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update user', [$e], 200);
        }
    }
}
