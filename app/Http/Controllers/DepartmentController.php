<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\ErrorHandler\Debug;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use function Ramsey\Uuid\v2;

class DepartmentController extends Controller
{

    public function index(Request $request)
    {
        $departments = Department::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('department_head', 'like', "%{$search}%");
            })
            ->get();

        return Inertia::render('Admin/Departments/Index', [
            'departments' => $departments,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:15',
            'code' => 'required|max:7',
            'department_head' => 'required',
            'description' => 'nullable|max:200'
        ]);

        $department = new Department($validated);
        $department->description = $request->description ?? 'Description is here';
        $department->save();
        flash()->option('position', 'bottom-center')->success('Department Added Successfully');
        return Inertia::location('/departments');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|max:15',
        'code' => 'required|max:7',
        'department_head' => 'required',
        'description' => 'nullable|max:200',
    ]);

    $department = Department::create($validated);  // Using mass assignment
    flash()->option('position', 'bottom-center')->success('Department Added Successfully');
    return Inertia::location('/departments');
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
    public function edit($id)
    {
        $department = Department::findOrFail($id);

        return Inertia::render('Admin/Departments/Edit', [
            'department' => $department
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:6',
        'department_head' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

        $department = Department::findOrFail($id);
        $department->update($validated);
        flash()->option('position','bottom-center')->success("Department Updated Successfully");
        return Inertia::location('/departments');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $department = Department::find($id);
        $department->delete();
        flash()->option('position', 'bottom-center')->error("Department Deleted Successfully");
        return Inertia::location('/departments');
    }

    public function trashdepartments()
    {
        $departments = Department::onlyTrashed()->get();
        return Inertia::render('Admin/Departments/TrashBin', ['departments' => $departments]);
    }

    public function RestoreAllTrashed()
    {

        $department = Department::onlyTrashed()->restore();
        if ($department) {
            flash()->option('position', 'bottom-center')->info("Departments Restore Successfully");
            return Inertia::location('/departments');
        } else {
            flash()->option('position', 'bottom-center')->option('timeout', 1000)->info('no Trashed Data found!');
            return Inertia::location('/trashdepartments');
        }
    }

    public function RestoreOneDepartment($id)
    {
        $department = Department::withTrashed()->find($id)->restore();
        flash()->option('position', 'bottom-center')->success("Department Restore Successfully");
        return Inertia::location('/departments');
    }

    public function DeleteOneDepartment($id)
    {
        Department::withTrashed()->find($id)->forceDelete();
        flash()->option('position', 'bottom-center')->error("Department Deleted Successfully");
        return Inertia::location('/departments');
    }

    public function printAll(Request $request)
    {
        $locale = $request->get('lang', 'en'); // Get it from the URL
    App::setLocale($locale);
        $departments = Department::with(['employees'])->latest()->paginate(10);
        return view('departments.print-all', compact('departments', 'locale'));
    }
}
