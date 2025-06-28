<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Distribution;
use App\Models\Employee;
use App\Models\ExperimentalEquipmentsReturn;
use App\Models\ExperimentalEquipmentsTransfer;
use App\Models\Leave;
use App\Models\Store;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {

            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'employees_count' => Employee::count(),
                    'departments_count' => Department::count(),
                    'pending_leaves' => Leave::where('status', 'pending')->count(),
                    'supplies_count' => Supply::count(),
                    'store_items' => Store::count(),
                    'distributions' => Distribution::count(),
                    'transfers' => ExperimentalEquipmentsTransfer::count(),
                    'returns' => ExperimentalEquipmentsReturn::count(),
                    'users' => auth()->user()->role === 'manager' ? User::count() : null,
                ],
                'recent_employees' => Employee::latest()->take(5)->get(['id', 'name', 'created_at']),
            ]);

    }

    public function backup()
    {
         return Inertia::render('Admin/Backup/Index');
    }
}
