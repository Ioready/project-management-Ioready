<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'country',
        'state',
        'city',
        'zip_code',
        'created_by',
    ];
}
