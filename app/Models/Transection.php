<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transection extends Model
{
    use HasFactory;
    protected $table = 'qp_transection';
    protected $fillable = [
        'transection_id',
        'order_id',
        'total_amount', 
        'transaction_type',
        'dis_amount',
        'user_id',
        'transection_status'
    ];
}
