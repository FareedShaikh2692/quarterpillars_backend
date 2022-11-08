<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExploerRegistration extends Model
{
    use HasFactory;
    protected $table = 'qp_exploer';
    protected $fillable = [
        'explore_id',
        "name",
        "username", 
        "dob",
        "gender",
        "avatar",
        "address_type",
        "location",
        "land_mark",
        "address_1",
        "address_2",
        "pin_code",
        "city",
        "state",
        "country",
        "is_email_verified",
        "is_mobile_verified",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
}
