<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QpUserCart extends Model
{
    use HasFactory;
    protected $table = 'qp_user_addcart';
    protected $fillable = [
        'user_id',
        'product_id',
        'color',
        'size',
        'qty',
        'dis_amount',
        'total_amount',
    ];
}
