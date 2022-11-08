<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiserRegistration extends Model
{
    use HasFactory;
    protected $table = 'qp_advertiser';
    protected $fillable = [
        'advertiser_id',
        "name",
        "username",
        "gst",
        "avatar",
        "pan_card",
        "company_name",
        "company_website",
        "company_address",
        "campany_Owner_name",
        "is_email_verified",
        "is_mobile_verified",
        "is_gst_verified",
        "is_pan_card_verified",
        "is_company_verified",
        "is_mobile_verified",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
}
