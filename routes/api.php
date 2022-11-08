<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthInfluencerController;
use App\Http\Controllers\Auth\AuthExploreController;
use App\Http\Controllers\Auth\AuthBusinessController;
use App\Http\Controllers\Auth\AuthAdvertiserController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Business\BankDetailsController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Influencer\InfluencerPostController;
use App\Http\Controllers\Advertiser\AdvertisePostController;
use App\Http\Controllers\Auth\AdminController;
/*
|--------------------------------------------------------------------------
| API Routes
|---------------------------- ----------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1/')->group(function () {
  Route::get('users/get-all-user-type', [UserController::class, 'get_all_user_type']);
  Route::post('auth/get-otp', [AuthController::class, 'send_otp']);
  Route::post('auth/verify-otp', [AuthController::class, 'verify_otp']);

  Route::post('/test-product', [ProductController::class, 'test_product']);


  Route::post('auth/login', [AuthController::class, 'login']);

  Route::post('auth/forgot-password', [AuthController::class, 'forgot_password']);
  Route::post('auth/forgot-password-verify', [AuthController::class, 'forgot_password_verify']);

  Route::post('auth/change-password', [AuthController::class, 'change_password']);

  Route::post('auth/mobile-number', [AuthController::class, 'mobile_number']);
  Route::post('auth/mobile-number-verify', [AuthController::class, 'mobile_number_verify']);

  Route::post('auth/aadhar-number', [AuthController::class, 'aadhar_number']);
  Route::post('auth/aadhar-verify', [AuthController::class, 'aadhar_verification']);

  Route::post('auth/email-otp', [AuthController::class, 'email_otp']);
  Route::post('auth/email-verify', [AuthController::class, 'email_otp_verification']);

  Route::post('auth/username', [AuthController::class, 'check_username']);

  Route::post('explore/registration', [AuthExploreController::class, 'explore_registration']);


  Route::post('influencer/registration', [AuthInfluencerController::class, 'influencer_registration']);
  Route::post('influencer/edit_profile', [AuthInfluencerController::class, 'edit_influencer_details']);

  Route::post('advertiser/registration', [AuthAdvertiserController::class, 'advertiser_registration']);

  Route::post('business/registration', [AuthBusinessController::class, 'business_registration']);

  Route::get('auth/get-all-advertiser', [AuthController::class, 'get_all_advertiser']);
  Route::get('auth/get-all-influencer', [AuthController::class, 'get_all_influencer']);

  Route::get('auth/get-privilages/{id}', [AuthController::class, 'get_privilages']);
  Route::post('auth/add-sub-user', [AuthController::class, 'add_sub_user']);
  Route::post('auth/get-sub-user', [AuthController::class, 'get_sub_user']);

  Route::post('auth/add-user-address', [AuthController::class, 'add_user_address']);
  Route::post('auth/update-user-address', [AuthController::class, 'update_user_address']);
  Route::post('auth/delete-user-address', [AuthController::class, 'delete_user_address']);
  Route::post('auth/get-user-address', [AuthController::class, 'get_user_address']);

  Route::post('auth/add-to-cart', [AuthController::class, 'add_to_cart']);
  Route::post('auth/get-cart-item', [AuthController::class, 'get_cart_item']);
  Route::post('auth/update-cart-item', [AuthController::class, 'update_cart_item']);
  Route::post('auth/remove-item-from-cart', [AuthController::class, 'remove_item_from_cart']);

  Route::post('auth/device-token', [AuthController::class, 'saveDeviceToken']);
  Route::post('auth/push-notificaiton', [AuthController::class, 'push_notificaiton']);

  Route::post('auth/user-oder-genrate', [AuthController::class, 'user_oder_genrate']);
  Route::post('auth/user-oder-history', [AuthController::class, 'user_oder_history']);
  Route::post('auth/easeBuzz-payment-access-token', [AuthController::class, 'easeBuzz_payment_access_token']);




  // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  //     return $request->user();
  // });







  // Route::middleware( [ 'auth:api' ] )->group( function(){

  // admin routes
  Route::prefix('/admin')->group(function () {
    Route::get('/all-privilages', [AdminController::class, 'all_privilages']);
    Route::post('/add-privilages', [AdminController::class, 'add_privilages']);
    Route::post('/update-privilages', [AdminController::class, 'update_privilages']);
    Route::post('/delete-privilages', [AdminController::class, 'delete_privilages']);
  });
  // explore routes
  Route::prefix('/explore')->group(function () {
    Route::post('/edit_explore_profile', [AuthExploreController::class, 'edit_explore_profile']);

   



    


  });

  // business routes
  Route::prefix('/business')->group(function () {
    Route::post('/edit-bussiness-profile', [AuthBusinessController::class, 'edit_bussiness_profile']);
    Route::post('/add-bussiness-sub-user', [AuthBusinessController::class, 'add_bussiness_sub_user']);
    Route::get('/get-bussiness-sub-user', [AuthBusinessController::class, 'get_bussiness_sub_user']);
    Route::get('/get-categories', [CategoryController::class, 'get_all_categories']);

    Route::get('/get-all-business', [AuthBusinessController::class, 'get_all_business']);

    Route::get('/get-subcategories', [CategoryController::class, 'get_all_subcategories']);
    Route::post('/aadhar-number', [AuthController::class, 'aadhar_number']);
    Route::post('/aadhar-verify', [AuthController::class, 'aadhar_verification']);
    Route::post('/account-details', [AuthController::class, 'add_business_bank_details']);
    Route::post('/add-product', [ProductController::class, 'add_product']);
    Route::post('/update-product', [ProductController::class, 'update_product']);
    Route::post('/delete-product', [ProductController::class, 'delete_product']);
    Route::get('/get-all-product-details', [ProductController::class, 'get_all_product_details']);
    Route::get('/get-product-details/{id}', [ProductController::class, 'get_product_details']);
    Route::get('/all-products-in-travel', [ProductController::class, 'get_all_products_in_travel']);
    Route::get('/all-products-in-fashion', [ProductController::class, 'get_all_products_in_fasion']);
    Route::get('/all-products-in-lifestyle', [ProductController::class, 'get_all_products_in_lifestyle']);
    Route::get('/all-products-in-food', [ProductController::class, 'get_all_products_in_food']);

    Route::get('/get-product-by-id/{id}', [ProductController::class, 'get_product_by_id']);

    Route::post('/req-business-collobration', [AuthBusinessController::class, 'req_business_collobration']);
    Route::get('/request-business-collobration/{id}', [AuthBusinessController::class, 'request_business_collobration']);
  });


  // influencer routes
  Route::prefix('/influencer')->group(function () {
    Route::post('/edit-influencer-profile', [AuthInfluencerController::class, 'edit_influencer_profile']);
    Route::get('/get-all-influencers', [AuthInfluencerController::class, 'get_all_influencers']);
    Route::get('/get-all-influencers-by-id/{id}', [AuthInfluencerController::class, 'get_all_influencers_by_id']);
    Route::post('/get-influencer-collabration', [AuthInfluencerController::class, 'get_influencer_collabration']);
    Route::get('/get-req-influencer-collabration/{id}', [AuthInfluencerController::class, 'get_req_influencer_collabration']);
    Route::post('/res-business-collobration', [AuthInfluencerController::class, 'res_business_collobration']);
    Route::post('/influencer-post-product', [InfluencerPostController::class, 'influencer_post_product']);
    Route::post('/influencer-post-product-update', [InfluencerPostController::class, 'influencer_post_product_update']);
    Route::post('/influencer-post-product-delete', [InfluencerPostController::class, 'influencer_post_product_delete']);
    Route::get('/get-all-influencer-post', [InfluencerPostController::class, 'get_all_influencer_post']);
    Route::get('/get-influencer-post-by-id/{id}', [InfluencerPostController::class, 'get_influencer_post_by_id']);
  });

  // advertiser routes
  Route::prefix('/advertiser')->group(function () {
    Route::post('/edit_advertiser_profile', [AuthAdvertiserController::class, 'edit_advertiser_profile']);

    Route::post('/advertise-post', [AdvertisePostController::class, 'advertise_post']);
    Route::post('/advertise-update', [AdvertisePostController::class, 'advertise_update']);
    Route::post('/advertiser-delete', [AdvertisePostController::class, 'advertiser_delete']);
    Route::post('/get-advertise', [AdvertisePostController::class, 'get_advertise']);
    Route::post('/get-advertise-by-id', [AdvertisePostController::class, 'get_advertise_by_id']);
    Route::get('/get-all-advertise', [AdvertisePostController::class, 'get_all_advertise']);
  });
  // });
});
