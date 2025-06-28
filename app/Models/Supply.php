<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'product_name', 'quantity', 'unit', 'supplier_id',
        'recipient_id', 'product_type', 'date', 'description',
    ];
    public function supplier()
    {
        return $this->belongsTo(Employee::class, 'supplier_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }
  
}
