<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AadvertisePost extends Model
{
    use HasFactory;
    protected $table = 'qp_advertise_post';
    protected $fillable = [
        'advertiser_id',
        "advertise_name",
        "video",
        "advertise_title",
        "advertise_description",
        "advertise_tag",
        "advertise_type",
        "advertise_location",
        "advertise_goal",
        "advertise_target_audience",
        "advertise_audience_name",
        "advertise_audience_location",
        "advertise_audience_interest",
        "advertise_audience_gender",
        "advertise_audience_age",
        "budget",
        "duration",
        "post_status"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
