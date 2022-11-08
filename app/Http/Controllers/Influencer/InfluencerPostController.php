<?php

namespace App\Http\Controllers\Influencer;

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
use App\Models\BusinessProducts;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class InfluencerPostController extends CommonUtility
{
    private $Mahareferid = '';

    public function influencer_post_product(Request $request)
    {

        $rules = [
            'product_id' => 'required',
            'business_id' => 'required',
            'influencer_id' => 'required',
            'post_type' => 'required',
            'tag' => 'required',
            'title' => 'required',
            'description' => 'required',
            'product_image' => 'required',
            'product_video' => 'required',
            'location' => 'required',
            'status' => 'required',
            'likes' => 'required',
            'share' => 'required',
        ];
        try {
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return $this->get_response(true, $validation->errors()->first(), null, 200);
            } else {
                $array_img_paths = array();
                $array_video_paths = array();
                if ($request->has('product_image')) {
                    for ($i = 0; $i < sizeof($request->product_image); $i++) {
                        $file = $request->product_image[$i];
                        $exte = $file->extension();
                        $img_name = substr(md5(mt_rand()), 0, 7);
                        $path = 'image/' . $request->influencer_id . '/' . $img_name . "." . $exte;
                        $file->move(public_path('image/' . $request->influencer_id), $img_name . "." . $exte);
                        array_push($array_img_paths, $path);
                    }
                }
                if ($request->has('product_video')) {
                    for ($i = 0; $i < sizeof($request->product_video); $i++) {
                        $file = $request->product_video[$i];
                        $exte = $file->extension();
                        $vid_name = substr(md5(mt_rand()), 0, 7);
                        $path = 'video/' . $request->influencer_id . '/' . $vid_name . "." . $exte;
                        $file->move(public_path('video/' . $request->influencer_id), $vid_name . "." . $exte);
                        array_push($array_video_paths, $path);
                    }
                }
                $influencer_posts_detail                 = new InfluencerPosts;
                $influencer_posts_detail->product_id     = $request->product_id;
                $influencer_posts_detail->business_id    = $request->business_id;
                $influencer_posts_detail->influencer_id  =  $request->influencer_id;
                $influencer_posts_detail->post_type      = $request->post_type;
                $influencer_posts_detail->tag            = $request->tag;
                $influencer_posts_detail->title          = $request->title;
                $influencer_posts_detail->description    = $request->description;
                $influencer_posts_detail->image          = json_encode($array_img_paths);
                $influencer_posts_detail->video          = json_encode($array_video_paths);
                $influencer_posts_detail->location       = $request->location;
                $influencer_posts_detail->status         = $request->status;
                $influencer_posts_detail->likes          = $request->likes;
                $influencer_posts_detail->share          = $request->share;
                if ($influencer_posts_detail->save()) {
                    return $this->get_response(false, 'Post added successfully', ['post added' => true,'post_details' => InfluencerPosts::where('id', $influencer_posts_detail->id)->first()], 200);
                } else {
                    return $this->get_response(true, 'error', ['post added' => false], 200);
                }
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to create', ["msg" => $e], 200);
        }
    }
    public function influencer_post_product_update(Request $request)
    {
        try {  
            $influencer_post_details = InfluencerPosts::where('id', $request->post_id)->first();
           
            if ($influencer_post_details->status==='draft') {
                $array_img_paths = array();
                $array_video_paths = array();
                if ($request->has('product_image')) {
                    for ($i = 0; $i < sizeof($request->product_image); $i++) {
                        $file = $request->product_image[$i];
                        $exte = $file->extension();
                        $img_name = substr(md5(mt_rand()), 0, 7);
                        $path = 'image/' . $request->influencer_id . '/' . $img_name . "." . $exte;
                        $file->move(public_path('image/' . $request->influencer_id), $img_name . "." . $exte);
                        array_push($array_img_paths, $path);
                    }
                }
                if ($request->has('product_video')) {
                    for ($i = 0; $i < sizeof($request->product_video); $i++) {
                        $file = $request->product_video[$i];
                        $exte = $file->extension();
                        $vid_name = substr(md5(mt_rand()), 0, 7);
                        $path = 'video/' . $request->influencer_id . '/' . $vid_name . "." . $exte;
                        $file->move(public_path('video/' . $request->influencer_id), $vid_name . "." . $exte);
                        array_push($array_video_paths, $path);
                    }
                }
                InfluencerPosts::where('id', $request->post_id)
                ->update([
                    'post_type'   => isset($request->post_type)?$request->post_type:$influencer_post_details->post_type,
                    'tag'         => isset($request->tag)?$request->tag:$influencer_post_details->tag,
                    'title'       => isset($request->title)?$request->title:$influencer_post_details->title,
                    'image'       => isset($request->product_image)?$array_img_paths:$influencer_post_details->image,
                    'video'       => isset($request->product_video)?$array_video_paths:$influencer_post_details->video,
                    'location'    => isset($request->location)?$request->location:$influencer_post_details->location,
                    'status'      => isset($request->status)?$request->status:$influencer_post_details->status,
                    'likes'       => isset($request->likes)?$request->likes:$influencer_post_details->likes,
                    'share'       => isset($request->share)?$request->share:$influencer_post_details->share,
                    'description' => isset($request->description)?$request->description:$influencer_post_details->description,
                ]);
                return $this->get_response(true, 'updated successfully', ['updated_success' => true, 'post_details' => InfluencerPosts::where('id', $request->post_id)->first()], 200);
            } else {
                InfluencerPosts::where('id', $request->post_id)
                    ->update([
                        'tag'         => $request->tag,
                        'description' => $request->description,
                    ]);
                return $this->get_response(true, 'updated successfully', ['updated_success' => true, 'post_details' => InfluencerPosts::where('id', $request->post_id)->first()], 200);
            }
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to update', ["msg" => $e], 200);
        }
    }

    public function influencer_post_product_delete(Request $request)
    {
        try {
            InfluencerPosts::where('id', $request->post_id)->delete();
            return $this->get_response(true, 'Deleted successfully', ['Deleted_success' => true, 'msg' => 'Post deleted successfully'], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to delete', ["msg" => $e], 200);
        }
    }

    public function get_all_influencer_post()
    {
        try {
            $influencerPosts = InfluencerPosts::get();
            foreach ($influencerPosts as $post) {
                $post['business_product'] = BusinessProducts::find($post->product_id);
            }
            return $this->get_response(true, 'successfully', ['success' => true, 'influencerPosts' => $influencerPosts], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get', ["msg" => $e], 200);
        }
    }

    public function get_influencer_post_by_id($id)
    {
        try {
            $influencerPosts = InfluencerPosts::find($id);
            return $this->get_response(true, 'successfully', ['success' => true, 'influencerPosts' => $influencerPosts], 200);
        } catch (Exception $e) {
            return $this->get_response(true, 'Failed to get', ["msg" => $e], 200);
        }
    }
}
