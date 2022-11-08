<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;

use App\Models\Otp;
use App\Models\User;
use App\Models\BusinessRegistration;
use App\Models\AdvertiserRegistration;
use App\Models\InfluencerRegistration;
use App\Models\ExploerRegistration;
use App\Models\BusinessAccountDetail;
use App\Models\AadhaarVerification;
use App\Models\BusinessProducts;
use App\Models\QpUserCart;
use App\Models\QPAddress;
use App\Models\Privilages;
use App\Models\QpUserOrders;
use App\Models\Transection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;
use Easebuzz;

class AuthController extends CommonUtility
{
    //


    private $Mahareferid = '';
    public function login(Request $request)
    {
        $rules = [
            'data' => 'required',
            'password' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);

        if (is_numeric($request->get('data'))) {
            $mobile_number = $request->data;
            $user = user::where('mobile_number', $mobile_number)->wher('role_id', $request->type)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    switch ($request->type) {
                        case 1:
                            $user_details = User::where('id', $user->id)->with('explore')->first();
                            break;
                        case 2:
                            $user_details = User::where('id', $user->id)->with('influencer')->first();
                            break;
                        case 3:
                            $user_details = User::where('id', $user->id)->with('advertiser')->first();
                            break;
                        case 4:
                            $user_details = User::where('id', $user->id)->with('business')->first();
                            break;
                        default:
                            return $this->get_response(true, 'enter valid number and password', null, 200);
                    }
                    return $this->get_response(false, 'mobile login successfully', ['login_success' => true, 'token' => $token, 'user' => $user_details], 200);
                } else {
                    return $this->get_response(true, 'enter valid number and password', null, 200);
                }
            } else {
                return $this->get_response(true, 'enter valid number and password', null, 200);
            }
        } elseif (filter_var($request->get('data'), FILTER_VALIDATE_EMAIL)) {
            $email = $request->data;
            $user = user::where('email', $email)->wher('role_id', $request->type)->first();
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                switch ($request->type) {
                    case 1:
                        $user_details = User::where('id', $user->id)->with('explore')->first();
                        break;
                    case 2:
                        $user_details = User::where('id', $user->id)->with('influencer')->first();
                        break;
                    case 3:
                        $user_details = User::where('id', $user->id)->with('advertiser')->first();
                        break;
                    case 4:
                        $user_details = User::where('id', $user->id)->with('business')->first();
                        break;
                    default:
                        return $this->get_response(true, 'enter valid number and password', null, 200);
                }
                return $this->get_response(false, 'email login successfully', ['login_success' => true, 'token' => $token, 'user' => $user_details], 200);
            } else {
                return $this->get_response(true, 'enter valid email and password', null, 200);
            }
        } else {
            $username = $request->data;
            $user = user::where('name', $username)->wher('role_id', $request->type)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    switch ($request->type) {
                        case 1:
                            $user_details = User::where('id', $user->id)->with('explore')->first();
                            break;
                        case 2:
                            $user_details = User::where('id', $user->id)->with('influencer')->first();
                            break;
                        case 3:
                            $user_details = User::where('id', $user->id)->with('advertiser')->first();
                            break;
                        case 4:
                            $user_details = User::where('id', $user->id)->with('business')->first();
                            break;
                        default:
                            return $this->get_response(true, 'enter valid number and password', null, 200);
                    }
                    return $this->get_response(false, ' username login successfully', ['login_success' => true, 'token' => $token, 'user' => $user_details], 200);
                } else {
                    return $this->get_response(true, 'enter valid username and password', null, 200);
                }
            } else {
                return $this->get_response(true, 'enter valid username and password', null, 200);
            }
        }
    }

    public function mobile_number(Request $request)
    {
        $rules = [
            'mobile_number' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 400);
            } else {
                $otp = mt_rand(1000, 9999);
                $sms_content = 'Your OTP is ' . $otp;
                $otps = new Otp;
                $otps->email = $request->email;
                $otps->mobile_number = $request->mobile_number;
                $otps->otp_type = 'Login/Signup';
                $otps->otp = $otp;
                $request->merge(['otp' => $otp, 'otp_type' => '']);
                if ($otps->save()  && $this->send_sms($request->mobile_number, $sms_content)) {
                    return $this->get_response(null, 'Otp sent successfully', ['otp' => $otp], 200);
                }
                return $this->get_response(true, 'Otp not sent', null, 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Otp not sent', [$e], 200);
        }
    }

 
    public function mobile_number_verify(Request $request)
    {
        $rules = [
            'mobile_number' => 'required',
            'otp' => 'required',
        ];
        try {
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                if (Otp::where(['mobile_number' => $request->mobile_number, 'otp' => $request->otp])->first()) {
                    $user = User::where(['mobile_number' => $request->mobile_number])->first();
                    if ($user) {
                        switch ($user->role_id) {
                            case 1:
                                $Exploer_details = ExploerRegistration::where(['username' => $user->name])->first();
                                $Exploer_details->is_mobile_verified = true;
                                $Exploer_details->save();
                                Otp::where(['mobile_number' => $request->mobile_number])->delete();
                                $user_details = User::where('id', $user->id)->with('explore')->first();
                                return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                                break;
                            case 2:
                                $Influencer_details = InfluencerRegistration::where(['username' => $user->name])->first();
                                $Influencer_details->is_mobile_verified     = true;
                                $Influencer_details->save();
                                Otp::where(['mobile_number' => $request->mobile_number])->delete();
                                $user_details = User::where('id', $user->id)->with('influencer')->first();
                                return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                                break;
                            case 3:
                                $Advertiser_details = AdvertiserRegistration::where(['username' => $user->name])->first();
                                $Advertiser_details->is_mobile_verified = true;
                                $Advertiser_details->save();
                                Otp::where(['mobile_number' => $request->mobile_number])->delete();
                                $user_details = User::where('id', $user->id)->with('advertiser')->first();
                                return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                                break;
                            case 4:
                                $busnisse_details = BusinessRegistration::where(['username' => $user->name])->first();
                                $busnisse_details->is_phone_no_verified = true;
                                $busnisse_details->save();
                                Otp::where(['mobile_number' => $request->mobile_number])->delete();
                                $user_details = User::where('id', $user->id)->with('business')->first();
                                return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                                break;
                        }  
                    } 
                    Otp::where(['mobile_number' => $request->mobile_number])->delete();
                    return $this->get_response(false, 'User not Found', ['token' => '', 'userExist' => false], 200);
                } else {
                    return $this->get_response(true, 'OTP not matched', null, 200);
                }
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'failed to varify', [$e], 200);
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
                $busnisse_details = BusinessRegistration::where(['owner_phone' => $request->mobile_number])->first();
                $busnisse_details->is_adhar_verifed = true;
                $busnisse_details->save();
                if ($user) {
                    AadhaarVerification::where(['business_id' => $request->business_id])->update(['is_aadhaar_verified' => 1]);
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    $banDetails = BusinessAccountDetail::where(['business_id' => $user->id])->first();
                    return $this->get_response(false, 'User Found', ['token' => $token, 'busnisse_details' => $busnisse_details, 'has_bank_details' => $banDetails ? $banDetails->id : false], 200);
                }

                return $this->get_response(false, 'User not Found', ['token' => ''], 200);
            } else {
                return $this->get_response(true, 'OTP did not matched', null, 200);
            }
        }
    }

    public function email_otp(Request $request)
    {
        $rules = [
            'email' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 400);
            } else {
                $otp = mt_rand(1000, 9999);
                $sms_content = 'Your OTP is ' . $otp;
                $otps = new Otp;
                $otps->email = $request->email;
                $otps->otp_type = 'email-verification';
                $otps->otp = $otp;
                if ($otps->save()) {
                    $data = array('otp' => $otp, 'email' => $request->email);
                    Mail::send('mail', $data, function ($message) use ($data) {
                        $message->to($data['email'])->subject('Verify your email address');
                        $message->from('noreply@quarterpillars.com', 'quarterpillars');
                    });
                    return $this->get_response(null, 'Otp sent successfully', ['otp' => $otp], 200);
                }
                return $this->get_response(true, 'Otp not sent', null, 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Otp not sent', [$e], 200);
        }
    }

    public function email_otp_verification(Request $request)
    {
        $rules = [
            'email' => 'required',
            'otp' => '',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            if (Otp::where(['email' => $request->email, 'otp' => $request->otp])->first()) {
                $user = User::where(['email' =>  $request->email])->first();
                if ($user) {
                    switch ($user->role_id) {
                        case 1:
                            $Exploer_details = ExploerRegistration::where(['username' => $user->name])->first();
                            $Exploer_details->is_email_verified = true;
                            $Exploer_details->save();
                            Otp::where(['email' => $request->email])->delete();
                            $user_details = User::where('id', $user->id)->with('explore')->first();
                            return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                            break;
                        case 2:
                            $Influencer_details = InfluencerRegistration::where(['username' => $user->name])->first();
                            $Influencer_details->is_email_verified     = true;
                            $Influencer_details->save();
                            Otp::where(['email' => $request->email])->delete();
                            $user_details = User::where('id', $user->id)->with('influencer')->first();
                            return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                            break;
                        case 3:
                            $Advertiser_details = AdvertiserRegistration::where(['username' => $user->name])->first();
                            $Advertiser_details->is_email_verified = true;
                            $Advertiser_details->save();
                            Otp::where(['email' => $request->email])->delete();
                            $user_details = User::where('id', $user->id)->with('advertiser')->first();
                            return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                            break;
                        case 4:
                            $busnisse_details = BusinessRegistration::where(['username' => $user->name])->first();
                            $busnisse_details->is_owner_email_verified     = true;
                            $busnisse_details->save();
                            Otp::where(['email' => $request->email])->delete();
                            $user_details = User::where('id', $user->id)->with('business')->first();
                            return $this->get_response(false, 'User Found', ['userExist' => true, 'user' => $user, 'user_details' => $user_details], 200);
                            break;
                    }
                }
                Otp::where(['email' => $request->email])->delete();
                return $this->get_response(false, 'User not Found', ['token' => '', 'userExist' => false], 200);
            } else {
                return $this->get_response(true, 'OTP not matched', null, 200);
            }
        }
    }
    public function send_otp(Request $request)
    {
        $rules = [
            'mobile_number' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            $otp = mt_rand(1000, 9999);
            $sms_content = 'Your OTP is ' . $otp;
            $request->merge(['otp' => $otp, 'otp_type' => 'Login/Signup']);
            if (Otp::create($request->all()) && $this->send_sms($request->mobile_number, $sms_content)) {
                return $this->get_response(null, 'Otp sent successfully', ['otp' => $otp], 200);
            }
            return $this->get_response(true, 'Otp not sent', null, 200);
        }
    }
    public function verify_otp(Request $request)
    {
        $rules = [
            'mobile_number' => 'required',
            'otp' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        } else {
            if (Otp::where(['mobile_number' => $request->mobile_number, 'otp' => $request->otp])->first()) {
                $user = User::where(['mobile_number' => $request->mobile_number])->first();
                if ($user) {
                    $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                    $aadhaarVerification = AadhaarVerification::where(['business_id' => $user->id])->first();
                    $bankDetails = BusinessAccountDetail::where(['business_id' => $user->id])->first();
                    Otp::where(['mobile_number' => $request->mobile_number])->delete();
                    return $this->get_response(false, 'User Found', ['token' => $token, 'userExist' => true, 'user' => $user, 'aadhaarVerification' => $aadhaarVerification ? $aadhaarVerification->is_aadhaar_verified : 0, 'has_bank_details' => $bankDetails ? $bankDetails->id : 0], 200);
                }
                Otp::where(['mobile_number' => $request->mobile_number])->delete();
                return $this->get_response(false, 'User not Found', ['token' => '', 'userExist' => false], 200);
            } else {
                return $this->get_response(true, 'OTP not matched', null, 200);
            }
        }
    }

    public function check_username(Request $request)
    {
        $rules = [
            'username' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                $user = User::where('name', $request->username)->first();
                if ($user) {
                    return $this->get_response(true, 'This username exit try another', null, 200);
                } else {
                    return $this->get_response(true, 'not matched', null, 200);
                }
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'not matched', ["msg" => $e], 200);
        }
    }

    public function forgot_password(Request $request)
    {
        $rules = [
            'data' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                if (is_numeric($request->get('data'))) {
                    $otp = mt_rand(1000, 9999);
                    $sms_content = 'Your OTP is ' . $otp;
                    $otps = new Otp;
                    $otps->mobile_number = $request->data;
                    $otps->otp_type = 'forgot-password';
                    $otps->otp = $otp;
                    if ($otps->save() && $this->send_sms($request->data, $sms_content)) {
                        return $this->get_response(null, 'Otp sent successfully', ['otp' => $otp, 'otp_id' => $otps->id], 200);
                    }
                    return $this->get_response(true, 'Otp not sent', ["msg" => $otp], 200);
                } elseif (filter_var($request->get('data'), FILTER_VALIDATE_EMAIL)) {
                    $otp = mt_rand(1000, 9999);
                    $sms_content = 'Your OTP is ' . $otp;
                    $otps = new Otp;
                    $otps->email = $request->data;
                    $otps->otp_type = 'forgot-password';
                    $otps->otp = $otp;
                    if ($otps->save()) {
                        $data = array('otp' => $otp, 'email' => $request->data);
                        Mail::send('forgotpassword', $data, function ($message) use ($data) {
                            $message->to($data['email'])->subject('Forgot password');
                            $message->from('noreply@quarterpillars.com', 'quarterpillars');
                        });
                        return $this->get_response(null, 'Otp sent successfully', ['otp' => $otp, 'otp_id' => $otps->id], 200);
                    }
                } else {
                    return $this->get_response(true, 'error', ["msg" => 'please enter valid email or mobile number'], 200);
                }
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'errorr', [$e], 200);
        }
    }
    public function forgot_password_verify(Request $request)
    {
        $rules = [
            'otp_id' => 'required',
            'otp' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            $otp_data = Otp::where('id', $request->otp_id)->where('otp', $request->otp)->first();
            if ($otp_data) {
                if ($otp_data->mobile_number) {
                    $user_details = User::where('mobile_number', $otp_data->mobile_number)->first();
                } else {
                    $user_details = User::where('email', $otp_data->email)->first();
                }
                $otp_data->delete();
                return $this->get_response(false, 'sucess', ['msg', 'OTP matched', 'user_details' => $user_details], 200);
            } else {
                return $this->get_response(true, 'error', ['msg' => 'OTP not matched'], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'errorr', [$e], 200);
        }
    }

    public function change_password(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'new_password' => 'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {

                $password = Hash::make($request->new_password);
                User::where('id', $request->user_id)
                    ->update([
                        'password' => $password
                    ]);
                $user = User::where('id', $request->user_id)->first();

                switch ($user->role_id) {
                    case 1:
                        $user_details = User::where('id', $user->id)->with('explore')->first();
                        break;
                    case 2:
                        $user_details = User::where('id', $user->id)->with('influencer')->first();
                        break;
                    case 3:
                        $user_details = User::where('id', $user->id)->with('advertiser')->first();
                        break;
                    case 4:
                        $user_details = User::where('id', $user->id)->with('business')->first();
                        break;
                    default:
                        return $this->get_response(true, 'enter valid number and password', null, 200);
                }

                $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                return $this->get_response(false, 'Password updated successfully', ['password_success' => true, 'token' => $token, 'user_details' => $user_details], 200);
            }
            return $this->get_response(true, $validation->errors()->first(), null, 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'errorr', [$e], 200);
        }

        //'password'=>
    }
    public function business_registration(Request $request)
    {
        $rules = [
            'catorige' => 'required',
            'company_name' => 'required',
            'owner_name' => 'required',
            'business_email' => 'required',
            'owner_email' => 'required',
            'owner_phone' => 'required',
            'is_biz_email_verified' => 'required',
            'is_owner_email_verified' => 'required',
            'is_phone_no_verified' => 'required',
            'owner_adhar' => 'required',
            'is_adhar_verifed' => 'required',
            'business_doc_id' => 'required',
            'is_business_doc_verfied' => 'required',
            'has_gst' => 'required',
            'is_gst_verfied' => 'required',
            'business_profile_pic' => 'required',
            'business_password' => 'required',
            'business_address' => 'required',
            'username' => 'required',
            'is_product' => 'required',
            'is_service' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->get_response(true, $validation->errors()->first(), null, 200);
        } else {
            if (User::create(['role_id' => 4, 'name' => $request->username, 'role_id' => 4, 'email' => $request->owner_email, 'mobile_number' => $request->owner_phone, 'password' => Hash::make($request->password)])) {
                $user = User::where(['mobile_number' => $request->owner_phone])->with('business')->first();
                $request->merge(['business_id' => $user->id]);
                BusinessRegistration::create($request->all());
                $user_details = User::find($user->id)->with('business')->first();
                $token = $user->createToken(env('ACCESS_TOKEN'))->accessToken;
                return $this->get_response(false, 'User created successfully', ['registration_success' => true, 'token' => $token, 'user_details' => $user_details], 200);
            }
            return $this->get_response(true, 'Failed to creaste user', null, 200);
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

    public function get_all_advertiser()
    {
        try {
            $all_Advertiser = AdvertiserRegistration::all();
            return $this->get_response(false, 'Advertiser', ['advertiser' => $all_Advertiser], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'OTP not matched', null, 200);
        }
    }
    public function get_all_influencer()
    {
        try {
            $all_Influencer = InfluencerRegistration::all();
            return $this->get_response(false, 'Influencer', ['advertiser' => $all_Influencer], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'OTP not matched', null, 200);
        }
    }

    public function get_privilages($id)
    {
        try {
            $Privilages = Privilages::where('role_id', $id)->get();
            return $this->get_response(false, 'Privilages', ['Privilages' => $Privilages], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Not found', ['error' => $e], 200);
        }
    }

    public function add_sub_user(Request $request)
    {
        $rules = [
            'business_id' => 'required',
            'role_id' => 'required',
            'username' => 'required',
            'email' => 'required',
            'mobile_number' => 'required',
            'privilages_id' => 'required',
            'is_active' => 'required',
            'is_sub_user' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {

                $auth_user = User::find($request->business_id);
                $randomString = Str::random(10);
                $password = Hash::make($randomString);

                User::create([
                    'role_id'       => $request->role_id,
                    'name'          => $request->username,
                    'email'         => $request->email,
                    'mobile_number' => $request->mobile_number,
                    'privilages'    => json_encode($request->privilages_id),
                    'is_active'     => $request->is_active,
                    'is_sub_user'   => $request->is_sub_user,
                    'owner_id'         => $request->business_id,
                    'password'      => $password
                ]);

                $privilages_id = $request->privilages_id;
                $array_privilage = [];
                foreach ($privilages_id as $id) {
                    $privilage = Privilages::where('privilages_id', $id)->first();
                    array_push($array_privilage, $privilage->privilages_name);
                }

                $data = array('owner' => $auth_user->name, 'privilages' => $array_privilage, 'email' => $request->email, 'password' => $randomString);

                Mail::send('subuser', $data, function ($message) use ($data) {
                    $message->to($data['email'])->subject('New user');
                    $message->from('noreply@quarterpillars.com', 'quarterpillars');
                });
                // $sms_content = 'username' . $request->username . 'password' . $randomString;
                // $this->send_sms($request->mobile_number, $sms_content);
                return $this->get_response(null, 'Created successfully', ['userdetails' => $data], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Not found', ['error' => $e], 200);
        }
    }
    public function get_sub_user(Request $request)
    {
        $rules = [
            'role_id' => 'required',
            "owner_id" => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                $auth_sub_user = User::where('owner_id', $request->owner_id)->where('role_id', $request->role_id)->where('is_sub_user', true)->get();
                return $this->get_response(null, 'Created successfully', ['auth_sub_user' => $auth_sub_user], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Not found', ['error' => $e], 200);
        }
    }


    public function add_user_address(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'address_name' => 'required',
            'address_type' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'state' => 'required',
            'address' => 'required',
            'landmark' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                QPAddress::create($request->all());
                return $this->get_response(true, 'successfully', ["msg" => "address successfully"], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to add', ["msg" => $e], 200);
        }
    }
    public function update_user_address(Request $request)
    {
        try {
            $explore_address = QPAddress::find($request->user_address_id);
            QPAddress::where('id', $request->user_address_id)
                ->update([
                    'address_name' => isset($request->address_name) ? $request->address_name : $explore_address->address_name,
                    'address_type' =>  isset($request->address_type) ? $request->address_type : $explore_address->address_type,
                    'zip_code'     =>  isset($request->zip_code) ? $request->zip_code : $explore_address->zip_code,
                    'city'         =>  isset($request->city) ? $request->city : $explore_address->city,
                    'state'        =>  isset($request->state) ? $request->state : $explore_address->state,
                    'address'      =>  isset($request->address) ? $request->address : $explore_address->address,
                    'landmark'     =>  isset($request->landmark) ? $request->landmark : $explore_address->landmark,
                ]);
            return $this->get_response(true, 'successfully', ["msg" => "address updated successfully"], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update', ["msg" => $e], 200);
        }
    }
    public function delete_user_address(Request $request)
    {
        try {
            QPAddress::find($request->user_address_id)->delete();
            return $this->get_response(true, 'successfully', ["msg" => "address deleted successfully"], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to delete', ["msg" => $e], 200);
        }
    }

    public function get_user_address(Request $request)
    {
        try {
            $explore_address = QPAddress::where('user_id', $request->user_id)->get();
            return $this->get_response(true, 'successfully', ["explore_address" => $explore_address], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to delete', ["msg" => $e], 200);
        }
    }

    public function easeBuzz_payment_access_token(Request $request)
    {
        
        include_once('../easebuzz-lib/easebuzz_payment_gateway.php');
        try {
            $MERCHANT_KEY = "2PBP7IABZ2";
            $SALT = "DAH88E3UWQ";
            $ENV = "test";

            $easebuzzObj = new Easebuzz($MERCHANT_KEY, $SALT, $ENV);
            $txnid = 'QPTRANS' . $request->user_id . rand(10, 1000);
           
            switch ($request->user_type) {
                case 1:
                    $user_details = User::where('id', $request->user_id)->with('explore')->first();
                    $user_adress = QPAddress::where('id', $request->address_id)->first();
                    $postData = array(
                        "txnid" => $txnid,
                        "amount" => $request->amount,
                        "firstname" => $user_details->name,
                        "email" => $user_details->email,
                        "phone" => $user_details->mobile_number,
                        "productinfo" => "Quarterpillar product",
                        "surl" => "http://localhost:3000/response.php",
                        "furl" => "http://localhost:3000/response.php",
                        "address1" => $user_adress->address_name,
                        "city" =>  $user_adress->city,
                        "state" => $user_adress->state,
                        "country" => "india",
                        "zipcode" => $user_adress->zip_code
                    );
                    break;
                case 2:                   
                    $user_details = User::where('id', $request->user_id)->with('influencer')->first();
                    $user_adress = QPAddress::where('id', $request->address_id)->first();
                    $postData = array(
                        "txnid" => $txnid,
                        "amount" => $request->amount,
                        "firstname" => $user_details->name,
                        "email" => $user_details->email,
                        "phone" => $user_details->mobile_number,
                        "productinfo" => "Quarterpillar product",
                        "surl" => "http://localhost:3000/response.php",
                        "furl" => "http://localhost:3000/response.php",
                        "address1" => $user_adress->address_name,
                        "city" =>  $user_adress->city,
                        "state" => $user_adress->state,
                        "country" => "india",
                        "zipcode" => $user_adress->zip_code
                    );
                    break;
                case 3:
                    
                    $user_details = User::where('id', $request->user_id)->with('advertiser')->first();

                    $postData = array(
                        "txnid" => $txnid,
                        "amount" => $request->amount,
                        "firstname" => $user_details->name,
                        "email" => $user_details->email,
                        "phone" => $user_details->mobile_number,
                        "productinfo" => "Quarterpillar product",
                        "surl" => "http://localhost:3000/response.php",
                        "furl" => "http://localhost:3000/response.php",
                        // "address1" => $user_adress->address_name,
                        // "city" =>  $user_adress->city,
                        // "state" => $user_adress->state,
                        // "country" => "india",
                        // "zipcode" => $user_adress->zip_code
                    );

                    break;
                    // case 4:
                    //     $user_details = User::where('id', $request->user_id)->with('business')->first();
                    //     break;
                default:
                    return $this->get_response(true, 'invalid user type', null, 200);
            }





            $res = $easebuzzObj->initiatePaymentAPI($postData);
            return $this->get_response(false, 'successfully', ["payment" => $res], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to create payment key', ["msg" => $e], 200);
        }
    }
    // public function aadhar_number(Request $request){
    //     $rules = [
    //         'aadhaar_number'=>'required'
    //     ];
    //     $validation = Validator::make($request->all(), $rules);
    //     if($validation->fails()){
    //         return $this->get_response(true, $validation->errors()->first(), null, 400);
    //     }
    //     else{
    //         if($response = $this->aadhaar_otp($request->aadhaar_number)){
    //             $data = json_decode($response, true);
    //             if($data){
    //                 if(AadhaarVerification::where(['aadhaar_number'=>$request->aadhaar_number])->first()){

    //                 }
    //                 else{
    //                     AadhaarVerification::create($request->all());
    //                 }
    //                 $this->Mahareferid = 0;
    //                 return $this->get_response(false, 'Otp sent successfully', ['Mahareferid'=>$data['Data']], 200);
    //             }
    //             else{
    //                 return $this->get_response(true, 'Otp faild to send', ['otp_send'=>false], 200);
    //             }

    //         }
    //         return $this->get_response(true, 'Otp not sent', ['otp_send'=>false], 200);
    //     }
    // }
    // public function aadhar_verification(Request $request){
    //     $rules = [
    //         'otp'=>'required',
    //     ];


    //     $validation = Validator::make($request->all(), $rules);

    //     if($validation->fails()){		
    //         return $this->get_response(true, $validation->errors()->first(), null, 400);
    //     }
    //     else{

    //         $response = $this->aadhaar_verification($request->otp, $request->Mahareferid);
    //         return $data = json_decode($response, true);
    //         if(count($data['Data'])){
    //             $user = User::where(['mobile_number'=>$request->mobile_number])->first();

    //             if($user){
    //                 AadhaarVerification::where(['business_id'=>$request->business_id])->update(['is_aadhaar_verified'=>1]);
    //                 $token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
    // 				$banDetails = BusinessAccountDetail::where(['business_id'=>$user->id])->first();
    //                 return $this->get_response(false, 'User Found', ['token'=>$token, 'has_bank_details'=>$banDetails?$banDetails->id:false], 200 );
    //             }

    //             return $this->get_response(false, 'User not Found', ['token'=>''], 200);
    //         }
    //         else{
    //             return $this->get_response(true, 'OTP did not matched', null, 200);
    //         }
    //     }
    // }
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

    public function saveDeviceToken(Request $request)
    {
        try {
            User::where('id', $request->user_id)
                ->update([
                    'device_token' =>  $request->device_token,

                ]);
            return response()->json(['Device token saved successfully.']);
        } catch (Exception $e) {
            return $this->get_response(true, 'Device token Not saved', ['error' => $e], 200);
        }
    }

    public function push_notificaiton(Request $request)
    {
        if ($request->device_token) {
            $firebaseToken = $request->device_token;
        } else {
            $firebaseToken = User::where('id', $request->user_id)->select('device_token')->first();
        }

        $SERVER_API_KEY = 'AAAAdLYZPyI:APA91bFVhnrT3tUYJWS5aKMBM9ObqK4LBFIrhwS5CoHHKlnORXOIadVwpjE4QTXMKicbQTxifccSdphB2EF7Jw_jCkyjHciMHGlQ0zvufnNHtAifxqUgQ0Ww01XprMn8a2dVa4EKsNc8';
        $data = [
            "to" => $firebaseToken,
            "title" => $request->title,
            "body" => $request->body,
            "content_available" => true,
            "priority" => "high",
        ];
        $data1 = [
            "postBody" => [
                "notification" => [
                    "title" => $request->title,
                    "body" => $request->body,
                    "click_action" => null,
                    "icon" => null
                ],
                "data" => null,
                "to" => $firebaseToken
            ],
            "serverKey" =>  $SERVER_API_KEY
        ];
        $dataString = json_encode($data1);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        try {
            //https://fcm.googleapis.com/fcm/send
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://testfcm.com/api/notify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            return $this->get_response(false, 'Send successfully', ['msg' => $response], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Faild to send', ['error' => $e], 200);
        }
    }

    public function add_to_cart(Request $request)
    {
        $rules = [
            'product_id' => 'required',
            'user_id' => 'required',
            'color' => 'required',
            'size' => 'required',
            'qty' => 'required',
            'dis_amount' => 'required',
            'total_amount' => 'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        try {
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                QpUserCart::create($request->all());
                $product = BusinessProducts::where('id', $request->product_id)->first();
                BusinessProducts::where('id', $request->product_id)->update([
                    "qty" => $product->qty - $request->qty
                ]);
                return $this->get_response(true, 'successfully', ["msg" => "added successfully"], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to add', ["msg" => $e], 200);
        }
    }
    public function update_cart_item(Request $request)
    {
        try {
            $cart_item =  QpUserCart::where('cart_id', $request->cart_id)->first();
            QpUserCart::where('cart_id', $request->cart_id)
                ->update([
                    'color'        => isset($request->color) ? $request->color : $cart_item->color,
                    'size'         =>  isset($request->size) ? $request->size : $cart_item->size,
                    'qty'          =>  isset($request->qty) ? $request->qty : $cart_item->qty,
                    'dis_amount'   =>  isset($request->dis_amount) ? $request->dis_amount : $cart_item->dis_amount,
                    'total_amount' =>  isset($request->total_amount) ? $request->total_amount : $cart_item->total_amount,
                ]);
            return $this->get_response(true, 'updated successfully', ["msg" => "updated successfully"], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to updated', ["msg" => $e], 200);
        }
    }
    public function get_cart_item(Request $request)
    {
        try {
            $cart_item = QpUserCart::where('user_id', $request->user_id)->get();
            foreach ($cart_item as $item) {
                $item['product_details'] = BusinessProducts::where('id', $item->product_id)->first();
            }
            return $this->get_response(true, 'successfully', ["cart_item" => $cart_item], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to add', ["msg" => $e], 200);
        }
    }

    public function remove_item_from_cart(Request $request)
    {
        try {
            QpUserCart::where('cart_id', $request->cart_id)->delete();
            return $this->get_response(true, ' successfully', ["cart_item" => 'Remove successfully'], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to remove', ["msg" => $e], 200);
        }
    }


    public function user_oder_genrate(Request $request)
    {
        try {
            $transection_id = 'QPTRANS' . rand(10, 1000);
            $Orders_id = array();
            foreach ($request->cart_ids as $id) {              
                $cart_item = QpUserCart::where('cart_id', $id)->first();
                if($request->result==='user_cancelled'){
                    $order_item = [
                        'product_id' => $cart_item->product_id,
                        'user_id' => $request->user_id,
                        'color' => $cart_item->color,
                        'size' => $cart_item->size,
                        'qty' => $cart_item->qty,
                        'dis_amount' => $cart_item->dis_amount,
                        'total_amount' => $cart_item->total_amount,
                        'transection_id' => $transection_id,
                        'address_id' => $request->address_id,
                        'orders_status'=>'cancelled',
                    ];
                    
                }else if($request->result==='cod'){
                    $order_item = [
                        'product_id' => $cart_item->product_id,
                        'user_id' => $request->user_id,
                        'color' => $cart_item->color,
                        'size' => $cart_item->size,
                        'qty' => $cart_item->qty,
                        'dis_amount' => $cart_item->dis_amount,
                        'total_amount' => $cart_item->total_amount,
                        'transection_id' => $transection_id,
                        'address_id' => $request->address_id,
                        'orders_status'=>'cod',
                    ];
                }else{
                    $order_item = [
                        'product_id' => $cart_item->product_id,
                        'user_id' => $request->user_id,
                        'color' => $cart_item->color,
                        'size' => $cart_item->size,
                        'qty' => $cart_item->qty,
                        'dis_amount' => $cart_item->dis_amount,
                        'total_amount' => $cart_item->total_amount,
                        'transection_id' => $transection_id,
                        'address_id' => $request->address_id,
                        'orders_status'=>'pending',
                    ];
                }
               
                $order = QpUserOrders::create($order_item);
                array_push($Orders_id, $order->id);
            }
            $transection = [
                'transection_id' => $transection_id,
                'order_id' => implode(',', $Orders_id) ,
                'total_amount' => $request->total_amount,
                'transaction_type' => $request->transaction_type,
                'user_id' => $request->user_id,
                'dis_amount' => $request->dis_amount,
                'transection_status' => $request->result
            ];
           
            Transection::create($transection);
            return $this->get_response(false, 'successfully', ["orders" => 'done'], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to create oders', ["msg" => $e], 200);
        }
    }


    public function user_oder_history(Request $request)
    {
        $orders = QpUserOrders::where('user_id', $request->user_id)->with('product_details')->get();
        return $orders;
    }
}
