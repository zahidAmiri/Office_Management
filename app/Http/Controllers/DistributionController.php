<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Distribution;
use App\Models\Store;
use Inertia\Inertia;

class DistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Distribution/Index', [
            'distributions' => Distribution::with(['distributor', 'department'])->latest()->get(),
            'employees' => Employee::select('id', 'name', 'last_name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'storeItems' => Store::select('product_name', 'unit', 'quantity')->where('quantity', '>', 0)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Distribution/Create', [
            'employees' => Employee::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'storeItems' => Store::where('quantity', '>', 0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|exists:stores,product_name',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'distributed_by' => 'required|exists:employees,id',
            'received_by' => 'required|exists:departments,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $storeItem = Store::where('product_name', $validated['product_name'])
            ->where('unit', $validated['unit'])
            ->first();

        if (!$storeItem || $storeItem->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough quantity in store']);
        }

        $storeItem->decrement('quantity', $validated['quantity']);

        Distribution::create($validated);

        flash()->option('position', 'bottom-center')->success('Product distributed and store updated.');
        return Inertia::location('/distributions');
    }

    /**
     * Display the specified resource.
     */
    public function show(Distribution $distribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distribution $distribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distribution $distribution)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|exists:stores,product_name',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'distributed_by' => 'required|exists:employees,id',
            'received_by' => 'required|exists:departments,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
    
        $store = Store::where('product_name', $distribution->product_name)
            ->where('unit', $distribution->unit)
            ->first();
    
        if ($store) {
            // Restore the old quantity
            $store->increment('quantity', $distribution->quantity);
        }
    
        // Now check if new quantity is available
        $newStore = Store::where('product_name', $validated['product_name'])
            ->where('unit', $validated['unit'])
            ->first();
    
        if (!$newStore || $newStore->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough quantity in store']);
        }
    
        $newStore->decrement('quantity', $validated['quantity']);
    
        $distribution->update($validated);
    
        flash()->option('position', 'bottom-center')->success('Product distribution Updated Successfully');
        return Inertia::location('/distributions');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distribution $distribution)
    {
        $store = Store::where('product_name', $distribution->product_name)
        ->where('unit', $distribution->unit)
        ->first();

    if ($store) {
        $store->increment('quantity', $distribution->quantity);
    }

    $distribution->delete();

    flash()->option('position', 'bottom-center')->error('Product distribution deleted Successfully.');
    return Inertia::location('/distributions');

    }
}
