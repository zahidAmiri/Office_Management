<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\App;

class LeaveController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('leaveAllocations.leaveType')->get();   // clear
        $leaveTypes = LeaveType::all();

        return Inertia::render('Admin/LeaveAllocation/Index', [
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_days' => 'required|integer|min:1',
        ]);

        LeaveAllocation::create($validated);
        flash()->option('position', 'bottom-center')->success("Leave Allocated Successfully!");
        return Inertia::location('/leave');
    }

    public function printAll(Request $request)
    {
        $locale = $request->get('lang', 'en'); // Get it from the URL
        App::setLocale($locale); // Tell Laravel which language to use

        $leave_allocations = Employee::with(['leaveAllocations.leaveType'])->latest()->paginate(10);
        return view('leave_allocation.print-all', compact('leave_allocations'));
    }
}
