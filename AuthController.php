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
use Validator;
use Hash;
use Mail;
class AuthController extends CommonUtility
{
    //
    private $Mahareferid = '';
    public function send_otp(Request $request){
        $rules = [
            'mobile_number'=>'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            $otp = mt_rand(1000, 9999);
            $sms_content = 'Your OTP is '.$otp;
            $request->merge(['otp'=>$otp, 'otp_type'=>'Login/Signup']);
            if(Otp::create($request->all()) && $this->send_sms($request->mobile_number, $sms_content)){
                return $this->get_response(null, 'Otp sent successfully', ['otp'=>$otp], 200);
            }
            return $this->get_response(true, 'Otp not sent', null, 200);
        }
    }
    public function verify_otp(Request $request){
        $rules = [
            'mobile_number'=>'required',
            'otp'=>'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if(Otp::where(['mobile_number'=>$request->mobile_number, 'otp'=>$request->otp])->first()){
                $user = User::where(['mobile_number'=>$request->mobile_number])->first();
                if($user){
                    $token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
                    $aadhaarVerification = AadhaarVerification::where(['business_id'=>$user->id])->first();
                    $bankDetails = BusinessAccountDetail::where(['business_id'=>$user->id])->first();
                    Otp::where(['mobile_number'=>$request->mobile_number])->delete();
                    return $this->get_response(false, 'User Found', ['token'=>$token, 'userExist'=>true, 'user'=>$user, 'aadhaarVerification'=>$aadhaarVerification?$aadhaarVerification->is_aadhaar_verified:0, 'has_bank_details'=>$bankDetails?$bankDetails->id:0], 200 );
                }
                Otp::where(['mobile_number'=>$request->mobile_number])->delete();
                return $this->get_response(false, 'User not Found', ['token'=>'', 'userExist'=>false], 200);
            }
            else{
                return $this->get_response(true, 'OTP not matched', null, 200);
            }
        }
    }

    public function mobile_number(Request $request){
        $rules = [
            'mobile_number'=>'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            $otp = mt_rand(1000, 9999);
            $sms_content = 'Your OTP is '.$otp;
            $request->merge(['otp'=>$otp, 'otp_type'=>'Login/Signup']);
            if(Otp::create($request->all()) && $this->send_sms($request->mobile_number, $sms_content)){
                return $this->get_response(null, 'Otp sent successfully', ['otp'=>$otp], 200);
            }
            return $this->get_response(true, 'Otp not sent', null, 200);
        }
    }
    public function mobile_number_verify(Request $request){
        $rules = [
            'mobile_number'=>'required',
            'otp'=>'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if(Otp::where(['mobile_number'=>$request->mobile_number, 'otp'=>$request->otp])->first()){
                $user = User::where(['mobile_number'=>$request->mobile_number])->first();
                if($user){
                  //  $token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
                    Otp::where(['mobile_number'=>$request->mobile_number])->delete();
                    return $this->get_response(false, 'User Found', [ 'userExist'=>true, 'user'=>$user], 200 );
                }
                Otp::where(['mobile_number'=>$request->mobile_number])->delete();
                return $this->get_response(false, 'User not Found', ['token'=>'', 'userExist'=>false], 200);
            }
            else{
                return $this->get_response(true, 'OTP not matched', null, 200);
            }
        }
    }
    public function aadhar_number(Request $request){
        $rules = [
            'aadhaar_number'=>'required'
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if($response = $this->aadhaar_otp($request->aadhaar_number)){
                $data = json_decode($response, true);
                if($data){
                    if(AadhaarVerification::where(['aadhaar_number'=>$request->aadhaar_number])->first()){

                    }
                    else{
                        AadhaarVerification::create($request->all());
                    }
                    $this->Mahareferid = 0;
                    return $this->get_response(false, 'Otp sent successfully', ['Mahareferid'=>$data['Data']], 200);
                }
                else{
                    return $this->get_response(true, 'Otp faild to send', ['otp_send'=>false], 200);
                }
                
            }
            return $this->get_response(true, 'Otp not sent', ['otp_send'=>false], 200);
        }
    }
    public function aadhar_verification(Request $request){
        $rules = [
            'otp'=>'required',
        ];

	
        $validation = Validator::make($request->all(), $rules);
	
        if($validation->fails()){		
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
	
            $response = $this->aadhaar_verification($request->otp, $request->Mahareferid);
            return $data = json_decode($response, true);
            if(count($data['Data'])){
                $user = User::where(['mobile_number'=>$request->mobile_number])->first();
		
                if($user){
                    AadhaarVerification::where(['business_id'=>$request->business_id])->update(['is_aadhaar_verified'=>1]);
                    $token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
					$banDetails = BusinessAccountDetail::where(['business_id'=>$user->id])->first();
                    return $this->get_response(false, 'User Found', ['token'=>$token, 'has_bank_details'=>$banDetails?$banDetails->id:false], 200 );
                }
		
                return $this->get_response(false, 'User not Found', ['token'=>''], 200);
            }
            else{
                return $this->get_response(true, 'OTP did not matched', null, 200);
            }
        }
    }

    public function email_otp(Request $request){
        
        $rules = [
            'email'=>'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){		
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }else{
            $data = array('name'=>"Virat Gandhi",);
   
            Mail::send(['text'=>'mail'], $data, function($message) {
               $message->to('fareedshaikh2692@gmail.com', 'Tutorials Point')->subject
                  ('Laravel Basic Testing Mail');
               $message->from('xyz@gmail.com','Virat Gandhi');
            });
            dd('done');
        }
    }

    public function email_otp_verification(Request $request){
        dd($request->all());
        $rules = [
            'email'=>'required',
            'otp'=>'',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){		
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }else{

        }
    }
    public function business_registration(Request $request){
        $rules = [
            'catorige'=>'required',
            'company_name' => 'required',
            'owner_name' => 'required',
            'business_email'=>'required',
            'owner_email' => 'required',
            'owner_phone' => 'required',
            'is_biz_email_verified'=>'required',
            'is_owner_email_verified' => 'required',
            'is_phone_no_verified' => 'required',
            'is_adhar_verifed'=>'required',
            'business_doc_id' => 'required',
            'is_business_doc_verfied' => 'required',
            'has_gst'=>'required',
            'is_gst_verfied' => 'required',
            'business_profile_pic' => 'required',
            'business_password'=>'required',
            'business_address' => 'required',
            'username' => 'required',
            'is_product'=>'required',
            'is_service' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'catorige'=>'required'
        ];
        
        $validation = Validator::make($request->all(), $rules);
       
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if(User::create(['role_id'=>4, 'name'=>$request->owner_name, 'role_id'=>4, 'email'=>$request->owner_email, 'mobile_number'=>$request->owner_phone, 'password'=>Hash::make($request->password)])){
                $user = User::where(['mobile_number'=>$request->owner_phone])->first();
                $request->merge(['business_id'=>$user->id]);
                BusinessRegistration::create($request->all());
                // $BusinessRegistration                           = new BusinessRegistration;
                // $BusinessRegistration->business_id              = $user->id;
                // $BusinessRegistration->catorige                 = $request->catorige;
                // $BusinessRegistration->company_name             = $request->company_name;
                // $BusinessRegistration->owner_name               = $request->business_email;
                // $BusinessRegistration->business_email           = $request->business_email;
                // $BusinessRegistration->owner_email              = $request->owner_email;
                // $BusinessRegistration->owner_phone              = $request->owner_phone;
                // $BusinessRegistration->is_biz_email_verified    = $request->is_biz_email_verified;
                // $BusinessRegistration->is_owner_email_verified  = $request->is_owner_email_verified;
                // $BusinessRegistration->is_phone_no_verified     = $request->is_phone_no_verified;
                // $BusinessRegistration->owner_adhar              = $request->owner_adhar;
                // $BusinessRegistration->is_adhar_verifed         = $request->is_adhar_verifed;
                // $BusinessRegistration->business_doc_id          = $request->business_doc_id;
                // $BusinessRegistration->is_business_doc_verfied  = $request->is_business_doc_verfied;
                // $BusinessRegistration->has_gst                  = $request->has_gst;
                // $BusinessRegistration->is_gst_verfied           = $request->is_gst_verfied;
                // $BusinessRegistration->business_profile_pic     = $request->business_profile_pic;
                // $BusinessRegistration->business_password        = $request->business_password;
                // $BusinessRegistration->business_address         = $request->business_address;
                // $BusinessRegistration->username                 = $request->username;
                // $BusinessRegistration->is_product               = $request->is_product;
                // $BusinessRegistration->owner_adhar              = $request->owner_adhar;
                // $BusinessRegistration->is_service               = $request->is_service;
                // $BusinessRegistration->city                     = $request->city;
                // $BusinessRegistration->state                    = $request->state;
                // $BusinessRegistration->country                  = $request->country;
                // $BusinessRegistration->save();
                $token = $user->createToken( env( 'ACCESS_TOKEN' ) )->accessToken;
                return $this->get_response(false, 'User created successfully', ['registration_success'=>true, 'token'=>$token, 'user'=>$user], 200);
            }
            return $this->get_response(true, 'Failed to creaste user', null, 200);
        }
    }
    public function add_business_account_details(Request $request){
        $rules = [
            'gst_number'=>'required',
            'pan_number'=>'required',
            'bank_account_number'=>'required',
            'bank_account_holder_name'=>'required',
            'bank_ifsc_code'=>'required',
            'bank_branch'=>'required',
            'product_or_service_details'=>'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if(BusinessAccountDetail::create($request->all())){
                return $this->get_response(false, 'Bank detail successfully saved', ['saveBankDetails'=>true], 200);
            }
            else{
                return $this->get_response(true, 'Bank detail failed to saved', ['saveBankDetails'=>false], 400);
            }
        }
    }
  
    public function add_business_bank_details(Request $request){
        $rules = [
            'gst_number'=>'required',
            'pan_number'=>'required',
            'bank_account_number'=>'required',
            'bank_ifsc_code'=>'required',
            'bank_branch'=>'required',
            'product_or_service_details'=>'required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()){
            return $this->get_response(true, $validation->errors()->first(), null, 400);
        }
        else{
            if(BusinessAccountDetail::create($request->all())){
                return $this->get_response(false, 'Bank details added succewssfully', null, 200);
            }
            else{
                return $this->get_response(true, 'Failed to add bank account details', null, 400);
            }
        }
    }
}
