<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperimentalEquipmentsTransfer extends Model
{
    protected $fillable = [
        'product_name', 'product_type', 'unit', 'recipient_id', 'distributed_by', 'date',
        'quantity', 'product_condition', 'description', 'department_id', 'status'
    ];

    public function recipient()
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }
    
    public function distributor()
    {
        return $this->belongsTo(Employee::class, 'distributed_by');
    }
    
    public function equipmentReturn()
    {
        return $this->hasOne(ExperimentalEquipmentsReturn::class, 'transfer_id');
    }

    public function department()
{
    return $this->belongsTo(Department::class);
}
}
