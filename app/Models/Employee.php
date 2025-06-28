<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'last_name',
        'father_name',
        'ranking',
        'contact_number',
        'id_number',
        'card_number',
        'image',
        'description',
        'department_id',
        'deleted_at'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function leaveAllocations()
    {
        return $this->hasMany(LeaveAllocation::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function receivedTransfers()
{
    return $this->hasMany(ExperimentalEquipmentsReturn::class, 'recipient_name');
}

public function distributedTransfers()
{
    return $this->hasMany(ExperimentalEquipmentsTransfer::class, 'distributed_by');
}


}