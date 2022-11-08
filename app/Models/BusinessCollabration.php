<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCollabration extends Model
{
    use HasFactory;
    protected $table = 'qp_business_collabration';
    protected $fillable = [
        'business_id',
        "influencer_id",
        "status", 
    ];
    public function businessCollabration()
    {
        return $this->belongsTo(InfluencerRegistration::class,'	influencer_id','influencer_id');
    }
 
}
