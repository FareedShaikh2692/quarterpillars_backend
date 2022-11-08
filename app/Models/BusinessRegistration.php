<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRegistration extends Model
{
    use HasFactory;
    protected $table = 'business_registrations';
    protected $fillable = [
        'business_id',
        "catorige",
        "sub_catorige",
        "company_name",
        "owner_name",
        "business_email", 
        "owner_email",
        "owner_phone",
        "is_biz_email_verified",
        "is_owner_email_verified",
        "is_phone_no_verified",
        "owner_adhar",
        "is_adhar_verifed",
        "business_doc_id",
        "is_business_doc_verfied",
        "has_gst",
        "is_gst_verfied",
        "business_profile_pic",
        "business_password",
        "business_address",
        "username",
        "is_product",
        "is_service",
        "city",
        "state",
        "country"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
}
