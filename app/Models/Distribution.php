<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'product_name', 'quantity', 'unit',
        'distributed_by', 'received_by', 'date', 'description',
    ];

    public function distributor()
    {
        return $this->belongsTo(Employee::class, 'distributed_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'received_by');
    }
}
