<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedStore extends Model
{
    protected $fillable = [
        'product_name',
        'unit',
        'quantity',
        'condition',
        'description',
    ];
}
