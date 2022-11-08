<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QPAddress extends Model
{   
    use HasFactory;
    protected $table = 'qp_user_address';
    protected $fillable = [
        'user_id',
        'address_name',
        'address_type',
        'zip_code',
        'city',
        'state',
        'address',
        'landmark',
    ];
}
