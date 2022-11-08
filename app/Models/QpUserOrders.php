<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QpUserOrders extends Model
{
    use HasFactory;
    protected $table = 'qp_user_orders';
    protected $fillable = [
        'product_id',
        'user_id',
        'color',
        'size',
        'qty',
        'dis_amount',
        'total_amount',
        'transection_id',
        'address_id',
        'orders_status'
    ];
    public function product_details()
    {
        return $this->hasOne(BusinessProducts::class,'id','product_id');
    }
   
}
