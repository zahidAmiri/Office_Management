<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'leave_type_id', 'start_date', 'end_date', 'days', 'reason', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function leaveAllocation()
    {
        return $this->belongsTo(LeaveAllocation::class);
    }

    // Check if requested leave exceeds available allocation
    public function isWithinAllocation()
    {
        $allocation = LeaveAllocation::where('employee_id', $this->employee_id)->first();
        return $allocation && $allocation->remaining_days >= $this->days;
    }
}
