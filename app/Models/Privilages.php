<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilages extends Model
{
    use HasFactory;
    protected $table = 'qp_privilages';
    protected $fillable = [
        'role_id',
        'privilages_name',
        'active'
    ];
}
