<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'mobile_number',
        'email',
        'privilages',
        'is_active'    ,
        'is_sub_user'  ,
        'owner_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function business()
    {
        return $this->hasOne(BusinessRegistration::class,'business_id');
    }
    public function influencer()
    {
        return $this->hasOne(InfluencerRegistration::class,'influencer_id');
    }
    public function explore()
    {
        return $this->hasOne(ExploerRegistration::class,'explore_id');
    }
    public function advertiser()
    {
        return $this->hasOne(AdvertiserRegistration::class,'advertiser_id');
    }
    public function advertiser_post()
    {
        return $this->hasMany(AadvertisePost::class,'advertiser_id');
    }
    public function user_product() 
    {
        return $this->hasMany(BusinessProducts::class,'business_id');
    }
}
