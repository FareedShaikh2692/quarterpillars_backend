<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProducts extends Model
{
    use HasFactory;
    protected $table = 'qp_business_products';
    protected $fillable = [
        'product_category',
        'product_image',
        'product_video_url',
        'product_name',
        'product_brand',
        'unit_id', 
        'minimum_qty',
        'product_tags',
        'is_refundable',
        'is_cod',
        'product_description',
        'unit_price',
        'sales_price',
        'dicount',
        'product_type',
        'color_id',
        'size_id', 
        'units_id',
        'qty',
        'warning_qty',
        'product_tax',
        'tax_type',
        'service_company',
        'delivery_type_id',
        'pin_code'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function QpUserOrders()
    {
        return $this->belongsTo(QpUserOrders::class);
    }
}
