<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAccountDetail extends Model
{
    use HasFactory;
    protected $table = 'qp_business_bankdetails';
    protected $fillable = [
        'business_id',
        'gst_number',
        'pan_number',
        'bank_account_number',
        'bank_account_holder_name',
        'bank_ifsc_code',
        'bank_branch',
        'ac_type',
        'product_or_service_details',
    ];
}
