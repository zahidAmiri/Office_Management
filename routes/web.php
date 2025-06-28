<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveAllocationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Pail\ValueObjects\Origin\Console;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DistributionController;

use App\Exports\SuppliesExport;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExperimentalEquipmentsReturnController;
use App\Http\Controllers\ExperimentalEquipmentsTransferController;
use App\Http\Controllers\UsedStoreController;
use App\Models\LeaveAllocation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Str;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/supplies-by-date', [DashboardController::class, 'getSuppliesByDate']);

// Department Private routes
// Only MANAGER can manage departments
Route::middleware(['auth', 'verified', 'role:manager,office_admin'])->group(function () {
    Route::post('/departments', [DepartmentController::class, 'create'])->name('departments.create');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::get('/editdepartment/{id}', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/editdepartment/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::get('/RestoreAllTrashed', [DepartmentController::class, 'RestoreAllTrashed'])->name('departments.RestoreAllTrashed');
    Route::get('/RestoreOneDepartment/{id}', [DepartmentController::class, 'RestoreOneDepartment'])->name('departments.RestoreOneDepartment');
    Route::delete('/DeleteOneDepartment/{id}', [DepartmentController::class, 'DeleteOneDepartment'])->name('departments.DeleteOneDepartment');
    Route::get('/trashdepartments', [DepartmentController::class, 'trashdepartments'])->name('departments.trashdepartments');
    Route::get('/restore-trashed-employees', [EmployeeController::class, 'restoreAllTrashed'])->name('restoreAllTrashedEmployees');
    Route::get('/restore-one-employee/{id}', [EmployeeController::class, 'restoreOneEmployee'])->name('restoreOneEmployee');
    Route::delete('/delete-one-employee/{id}', [EmployeeController::class, 'deleteOneEmployee'])->name('deleteOneEmployee');
});


// Department Public route
// every one can manage departments
Route::get('/departments', [DepartmentController::class, 'index'])->middleware(['auth', 'verified'])->name('departments.index');


// Allow only viewing for everyone
Route::resource('employees', EmployeeController::class)
    ->only(['index']) // Or add 'show' too if needed
    ->middleware(['auth', 'verified']);


// Protect modify actions (manager only)
Route::resource('employees', EmployeeController::class)
    ->except(['index']) // Only allow manager to do the rest
    ->middleware(['auth', 'verified', 'role:manager']);

// Employee Print

Route::middleware(['auth','verified','role:manager,office_admin'])->group(function(){
    Route::get('/employee/print_all', [EmployeeController::class, 'printAll'])->name('department.printAll');
});

// Leave_Allocation Print

Route::middleware(['auth','verified','role:manager,office_admin'])->group(function(){
    Route::get('/leave_allocation/print_all', [LeaveController::class, 'printAll'])->name('leave_allocation.printAll');
});


// Leave_Allocation Print

Route::middleware(['auth','verified','role:manager,office_admin'])->group(function(){
    Route::get('/add_leave/print_all', [LeaveRequestController::class, 'printAll'])->name('add_leave.printAll');
});

// Trash Employee only for managers
Route::middleware(['auth','verified','role:manager'])->group(function(){
    Route::get('/trashedemployees', [EmployeeController::class, 'trashedemployees'])->name('trashedemployees');
    Route::get('/restore-trashed-employees', [EmployeeController::class, 'restoreAllTrashed'])->name('restoreAllTrashedEmployees');
    Route::get('/restore-one-employee/{id}', [EmployeeController::class, 'restoreOneEmployee'])->name('restoreOneEmployee');
    Route::delete('/delete-one-employee/{id}', [EmployeeController::class, 'deleteOneEmployee'])->name('deleteOneEmployee');
    Route::get('/trashedemployees', [EmployeeController::class, 'trashedemployees'])->name('trashedemployees');


//    backup route

//    backup route

   // Page (already have this)
   Route::get('/backup', [DashboardController::class, 'backup'])->middleware('auth');

   // → run backup
   Route::post('/backup/run', function () {
       Artisan::call('backup:run');
       return response()->json(['message' => 'Backup completed!']);
   });

   Route::get('/backup/files', function () {
       $disk  = Storage::disk('local');          // root = storage/app
       $files = $disk->allFiles();               // all files including in subfolders

       return collect($files)
           ->filter(fn ($f) => Str::endsWith($f, '.zip'))   // only zip files
           ->map(fn ($f) => [
               'name'          => basename($f),
               'size'          => $disk->size($f),
               'last_modified' => $disk->lastModified($f),
               'download_url'  => route('backup.download', ['file' => $f]),  // <-- full path here
               'delete_url'    => route('backup.delete', ['file' => $f]),
           ])
           ->sortByDesc('last_modified')
           ->values();
   });

   // → download
   Route::get('/backup/download/{file}', function ($file) {
       $disk = Storage::disk('local');  // storage/app/private

       // Check if the file exists
       if (!$disk->exists($file)) {
           abort(404, 'File not found');
       }

       // Return the file download response
       return $disk->download($file);
   })->where('file', '.*')->name('backup.download');


   Route::delete('/backup/delete/{file}', function ($file) {
       $disk = Storage::disk('local');

       if (!$disk->exists($file)) {
           return response()->json(['message' => 'File not found'], 404);
       }

       $disk->delete($file);

       return response()->json(['message' => 'File deleted successfully']);
   })->where('file', '.*')->name('backup.delete')->middleware('auth');



});


// Allow only viewing for everyone
Route::resource('/leave', LeaveController::class)
    ->only(['index']) // Or add 'show' too if needed
    ->middleware(['auth', 'verified']);


// Protect modify actions (manager and office_admin)
Route::resource('/leave', LeaveController::class)
    ->except(['index']) // Only allow manager to do the rest
    ->middleware(['auth', 'verified', 'role:manager,office_admin']);



// Allow only viewing for everyone
Route::resource('/addleave', LeaveRequestController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified']);

// Protect modify actions (manager and office_admin)
Route::resource('/addleave', LeaveRequestController::class)
->except(['index']) // Only allow manager to do the rest
->middleware(['auth', 'verified', 'role:manager,office_admin']);


// routes/web.php
Route::get('/used-store', [UsedStoreController::class, 'index'])
      ->middleware('auth')
      ->name('used-store.index');

// Store
// Allow only viewing for everyone
Route::resource('/store', StoreController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified', 'role:manager,office_admin,department_head']);


// Distribution
// Allow only viewing for everyone
Route::resource('/distributions', DistributionController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified']);

// Protect modify actions (manager and office_admin)
Route::resource('/distributions', DistributionController::class)
->except(['index']) // Only allow manager, office admin to do the rest
->middleware(['auth', 'verified', 'role:manager,office_admin']);



//supply print only for manager and office_admin
Route::middleware(['auth','verified','role:manager,office_admin'])->group(function(){
    Route::get('/supplies/print-all', [SupplyController::class, 'printAll'])->name('supplies.printAll')->middleware(['auth', 'verified', 'role:manager,office_admin']);
    Route::get('/supplies/{id}/print', [SupplyController::class, 'print'])->name('supplies.print');
});


//Department print only for manager and office_admin
Route::middleware(['auth','verified','role:manager,office_admin'])->group(function(){
    Route::get('/department/print_all', [DepartmentController::class, 'printAll'])->name('department.print_all');
    Route::get('/supplies/{id}/print', [SupplyController::class, 'print'])->name('supplies.print');
});


// Supply
// Allow only viewing for everyone
Route::resource('/supplies', SupplyController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified']);

// Protect modify actions (manager and office_admin)
Route::resource('/supplies', SupplyController::class)
->except(['index']) // Only allow manager, office admin to do the rest
->middleware(['auth', 'verified', 'role:manager,office_admin']);


// Experimental transfer
// Allow only viewing for everyone
Route::resource('/experimental-transfers', ExperimentalEquipmentsTransferController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified']);

// Protect modify actions (manager and office_admin)
Route::resource('/experimental-transfers', ExperimentalEquipmentsTransferController::class)
->except(['index']) // Only allow manager, office admin to do the rest
->middleware(['auth', 'verified', 'role:manager,office_admin']);



// Experimental Return
// Allow only viewing for everyone
Route::resource('/experimental-return', ExperimentalEquipmentsReturnController::class)
->only(['index']) // Or add 'show' too if needed
->middleware(['auth', 'verified']);

// Protect modify actions (manager and office_admin)
Route::resource('/experimental-return', ExperimentalEquipmentsReturnController::class)
->except(['index']) // Only allow manager, office admin to do the rest
->middleware(['auth', 'verified', 'role:manager,office_admin']);



Route::post('experimental-returns', [ExperimentalEquipmentsReturnController::class, 'store'])->name('experimental-returns.store')
->middleware(['auth', 'verified', 'role:manager,office_admin']);


Route::middleware(['auth','role:manager'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/test', function()
{
    $dep = Department::find(40);
    return $dep->employees->pluck('name');
});

Route::middleware(['auth', 'role:manager,office_admin'])->get('/test-role', function () {
    return Auth::user()->role;
});

Route::get('mytest', function () {
    return config('backup.archive_password');
});

Route::get('/admin/approve-login/{user}', function (App\Models\User $user) {
    $user->is_login_approved = true;
    $user->save();
    return redirect('/')->with('message', 'Login approved.');
});

require __DIR__.'/auth.php';
