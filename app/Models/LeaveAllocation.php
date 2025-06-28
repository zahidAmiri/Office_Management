<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'leave_type_id', 'total_days', 'used_days'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    // // Method to update remaining days after leave approval
    // public function updateRemainingDays($usedDays)
    // {
    //     $this->remaining_days -= $usedDays;
    //     $this->save();
    // }

    // Method to reverse remaining days when leave is rejected
    public function reverseRemainingDays($usedDays)
    {
        $this->remaining_days += $usedDays;
        $this->save();
    }
}
