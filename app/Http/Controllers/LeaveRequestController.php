<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use Illuminate\Support\Facades\App;

class LeaveRequestController extends Controller
{
    // Show all leave requests
    public function index()
    {

        $leaveRequests = Leave::with(['employee', 'leaveType'])->get()->map(function ($leave) {
            return [
                'id' => $leave->id,
                'employee' => $leave->employee,
                'leave_type' => $leave->leaveType,
                'leave_type_id' => $leave->leave_type_id,
                'days' => $leave->days,  // Changed to `days`
                'status' => $leave->status,
            ];
        });

        return Inertia::render('Admin/AddLeave/Index', [
            'leaveRequests' => $leaveRequests,
            'employees' => Employee::all(),
            'leaveTypes' => LeaveType::all(),
        ]);
    }

    // Store a new leave request
    public function store(Request $request)
    {

    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'leave_type_id' => 'required|exists:leave_types,id',
        'days' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $allocation = LeaveAllocation::where('employee_id', $validated['employee_id'])
        ->where('leave_type_id', $validated['leave_type_id'])
        ->first();

    if (!$allocation) {
        return back()->withErrors(['error' => 'No leave allocation found for this employee and leave type.']);
    }

    $usedDays = $allocation->used_days ?? 0;
    $remainingDays = $allocation->total_days - $usedDays;

    if ($remainingDays < $validated['days']) {
        return back()->withErrors(['error' => 'Not enough leave days available.']);
    }

    Leave::create($validated);

    // Update the used days
    $allocation->used_days = $usedDays + $validated['days'];
    $allocation->save();
    flash()->option('position', 'bottom-center')->success("Leave Request created successfully");
    return Inertia::location('/addleave');
    }

    public function printAll(Request $request)
    {

        $locale = $request->get('lang', 'en');
        App::setLocale($locale);

        $add_leaves = Leave::with(['employee', 'leaveType'])->latest()->get();

        return view('add_leave.print-all', compact('add_leaves'));
    }

}
