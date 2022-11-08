<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AadhaarVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'aadhaar_number',
        'is_aadhaar_verified',
    ];
}
