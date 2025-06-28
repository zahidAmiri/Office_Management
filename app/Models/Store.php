<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    // Store.php
     protected $table = 'stores'; // optional if your table is already named 'stores'

    protected $fillable = ['product_name', 'quantity', 'unit', 'description'];
}
