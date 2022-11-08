<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonUtility;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\AadvertisePost;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdvertisePostController extends CommonUtility
{
    //
    private $Mahareferid = '';


    public function advertise_post(Request $request)
    {

        $rules = [
            'advertiser_id' => 'required',
            'advertise_name' => 'required',
            'advertise_video' => 'required',
            'advertise_title' => 'required',
            'advertise_description' => 'required',
            'advertise_tag' => 'required',
            'advertise_type' => 'required',
            'advertise_location' => 'required',
        ];
        try {
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                $file = $request->advertise_video;
                $exte = $file->extension();
                $vid_name = substr(md5(mt_rand()), 0, 7);
                $path = 'Advertise/' . $request->advertiser_id . '/' . $vid_name . "." . $exte;
                $file->move(public_path('Advertise/' . $request->advertiser_id), $vid_name . "." . $exte);
                $request->merge(['video' => $path]);
                AadvertisePost::create($request->all());
                $user_details = User::where('id', $request->advertiser_id)->with('advertiser', 'advertiser_post')->get();
                return $this->get_response(false, 'Post added successfully', ['post_success' => true, 'user_details' => $user_details], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to creaste post', ["msg" => $e], 200);
        }
    }
    public function advertise_update(Request $request)
    {

        try {
            if (isset($request->advertise_video)) {
                $file = $request->advertise_video;
                $exte = $file->extension();
                $vid_name = substr(md5(mt_rand()), 0, 7);
                $path = 'Advertise/' . $request->advertiser_id . '/' . $vid_name . "." . $exte;
                $file->move(public_path('Advertise/' . $request->advertiser_id), $vid_name . "." . $exte);
                $request->merge(['image' => $path]);
            } 

            $advertise_post = AadvertisePost::where('id', $request->advertise_post_id)->first();

            AadvertisePost::where('id', $request->advertise_post_id)
                ->update([
                    'advertise_name'               => isset($request->advertise_name) ? $request->advertise_name : $advertise_post->advertise_name,
                    'video'                        => isset($request->advertise_video) ? $request->video : $advertise_post->video,
                    'advertise_title'              => isset($request->advertise_title) ? $request->advertise_title : $advertise_post->advertise_title,
                    'advertise_description'        => isset($request->advertise_description) ? $request->advertise_description : $advertise_post->advertise_description,
                    'advertise_tag'                => isset($request->advertise_tag) ? $request->advertise_tag : $advertise_post->advertise_tag,
                    'advertise_type'               => isset($request->advertise_type) ? $request->advertise_type : $advertise_post->advertise_type,
                    'advertise_location'           => isset($request->advertise_location) ? $request->advertise_location : $advertise_post->advertise_location,
                    'advertise_goal'               => isset($request->advertise_goal) ? $request->advertise_goal : $advertise_post->advertise_goal,
                    'advertise_target_audience'    => isset($request->advertise_target_audience) ? $request->advertise_target_audience : $advertise_post->advertise_target_audience,
                    'advertise_audience_name'      => isset($request->advertise_audience_name) ? $request->advertise_audience_name : $advertise_post->advertise_audience_name,
                    'advertise_audience_location'  => isset($request->advertise_audience_location) ? $request->advertise_audience_location : $advertise_post->advertise_audience_location,
                    'advertise_audience_interest'  => isset($request->advertise_audience_interest) ? $request->advertise_audience_interest : $advertise_post->advertise_audience_interest,
                    'advertise_audience_gender'    => isset($request->advertise_audience_gender) ? $request->advertise_audience_gender : $advertise_post->advertise_audience_gender,
                    'advertise_audience_age'       => isset($request->advertise_audience_age) ? $request->advertise_audience_age : $advertise_post->advertise_audience_age,
                    'budget'                       => isset($request->budget) ? $request->budget : $advertise_post->budget,
                    'duration'                     => isset($request->duration) ? $request->duration : $advertise_post->duration,
                    'post_status'                  => isset($request->post_status) ? $request->post_status : $advertise_post->post_status,
                ]);

            $user_details = User::where('id', $request->advertiser_id)->with('advertiser', 'advertiser_post')->get();
            return $this->get_response(false, 'Post updated successfully', ['post_success' => true, 'user_details' => $user_details], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to creaste post', ["msg" => $e], 200);
        }
    }

    public function advertiser_delete(Request $request)
    {
        try {
            AadvertisePost::find($request->advertise_post_id)->delete();
            return $this->get_response(false, 'successfully', ['msg' => 'Post deleted successfully '], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get delete', ["msg" => $e], 200);
        }
    }

    public function get_advertise(Request $request)
    {
        try {
            $post_details =  AadvertisePost::where('advertiser_id', $request->advertiser_id)->get();
            return $this->get_response(false, 'successfully', ['post_details' => $post_details], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get post', ["msg" => $e], 200);
        }
    }
    public function get_all_advertise()
    {
        try {
            $post_details =  AadvertisePost::all();
            return $this->get_response(false, 'successfully', ['post_details' => $post_details], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get post', ["msg" => $e], 200);
        }
    }
    public function get_advertise_by_id(Request $request)
    {
        try {
            $post_details =  AadvertisePost::find($request->advertise_post_id);
            return $this->get_response(false, 'successfully', ['post_details' => $post_details], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get post', ["msg" => $e], 200);
        }
    }
}
