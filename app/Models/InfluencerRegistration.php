<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerRegistration extends Model
{
    use HasFactory;
    protected $table = 'qp_influencer';
    protected $fillable = [
        'influencer_id',
        "name",
        "username",
        "dob", 
        "gender",
        "owner_phone",
        "avatar",
        "city",
        "state",
        "country",
        "insta_link",
        "insta_follower_count",
        "fb_link",
        "fb_follower_count",
        "youtube_link",
        "youtube_subscribers",
        "twitter_link",
        "twitter_follower_count",
        "tiktok_link",
        "tiktok_followers_count",
        "is_email_verified",
        "is_mobile_verified",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
}
 