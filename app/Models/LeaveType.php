<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function leaveAllocations()
    {
        return $this->hasMany(LeaveAllocation::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
