<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerPosts extends Model
{
    use HasFactory;
    protected $table = 'qp_influencer_posts';
    protected $fillable = [
        'product_id',
        "business_id",
        "influencer_id", 
        'post_type',
        "tag",
        "title", 
        'image',
        "video",
        "location", 
        'status',
        "likes",
        "share", 
        "description"
    ]; 

  
}
