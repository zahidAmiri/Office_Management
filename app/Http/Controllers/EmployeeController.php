<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Laravel\Pail\ValueObjects\Origin\Console;
use Illuminate\Database\Eloquent\Builder; // This is correct
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');


    $employees = Employee::with('department')
        ->when($search, function (Builder $query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->get();

    $departments = Department::all();

    return Inertia::render('Admin/Employees/Index', [
        'employees' => $employees,
        'departments' => $departments,
    ]);


        // $employees = Employee::with('department')->get(); // Eager load department
        // $departments = Department::all();

        // return Inertia::render('Admin/Employees/Index', [
        //     'employees' => $employees,
        //     'departments' => $departments,
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'ranking' => 'nullable|string|max:255',
            'contact_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:50',
            'card_number' => 'required|string|max:50',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('employees', 'public');
        }

        Employee::create($validated);
        return Inertia::location('/employees');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
         $validated = $request->validate([
           'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'father_name' => 'required|string|max:255',
        'contact_number' => 'required|string|max:255',
        'image' => 'nullable|image|max:1024', // Optional image, max size 1MB
        'department_id' => 'required|exists:departments,id',
        ]);

        // Update the employee
        $employee->update($validated);

        // Handle image upload if a new image is uploaded
        if ($request->hasFile('image')) {
            $employee->image = $request->file('image')->store('employees', 'public');
            $employee->save();
        }

        return Inertia::location('/employees');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $employees = Employee::find($id);
        if($employees)
        {
            $employees->delete();
            flash()->option('position', 'bottom-center')->error("Employee Deleted Successfully");
            return Inertia::location('/employees');
        }
    }


    public function trashedemployees()
    {
        $employees = Employee::onlyTrashed()->with('department')->get();

        return Inertia::render('Admin/Employees/TrashBin', [
            'employees' => $employees,
        ]);
    }

    public function restoreAllTrashed()
    {
        Employee::onlyTrashed()->restore();
        flash()->option('position', 'bottom-center')->success("Trashed Employees Restore Successfully");
        return Inertia::location('/employees');
    }

    public function restoreOneEmployee($id)
    {
        Employee::onlyTrashed()->where('id', $id)->restore();
        flash()->option('position', 'bottom-center')->success("Employees Restore Successfully");
        return Inertia::location('/employees');
    }

    public function deleteOneEmployee($id)
    {
        Employee::onlyTrashed()->where('id', $id)->forceDelete();
        flash()->option('position', 'bottom-center')->error("Employees deleted Successfully");
        return Inertia::location('/employees');

    }

    public function printAll(Request $request)
    {
        $locale = $request->get('lang', 'en'); // Get it from the URL
        App::setLocale($locale); // Tell Laravel which language to use

        $employees = Employee::with(['department'])->latest()->paginate(10);
        return view('employee.print-all', compact('employees'));
    }
}
