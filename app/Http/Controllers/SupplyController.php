<?php

namespace App\Http\Controllers;


use App\Models\Supply;
use App\Models\Store;
use App\Models\Employee;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;



class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Supply/Index', [
            'supplies' => Supply::with(['supplier', 'recipient'])->latest()->get(),
            'employees' => Employee::select('id', 'name', 'last_name')->get(),
        ]);
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
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'supplier_id' => 'required|exists:employees,id',
            'recipient_id' => 'required|exists:employees,id',
            'product_type' => 'nullable|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Create supply record
        Supply::create($validated);

        // Update or create store entry
        $store = Store::where('product_name', $validated['product_name'])->first();

        if ($store) {
            // Add to existing quantity
            $store->increment('quantity', $validated['quantity']);
        } else {
            Store::create([
                'product_name' => $validated['product_name'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'description' => $validated['description'],
            ]);
        }
        flash()->option('position', 'bottom-center')->success("Supply Added Successfully");
        return Inertia::location('/supplies');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supply $supply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supply $supply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'supplier_id' => 'required|exists:employees,id',
            'recipient_id' => 'required|exists:employees,id',
            'product_type' => 'nullable|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Adjust Store quantity if product name is unchanged
        if ($supply->product_name === $validated['product_name']) {
            $difference = $validated['quantity'] - $supply->quantity;

            $store = Store::where('product_name', $validated['product_name'])->first();
            if ($store) {
                $store->increment('quantity', $difference);
            }
        } else {
            // If product name changed: decrease old store, increase new store
            $oldStore = Store::where('product_name', $supply->product_name)->first();
            if ($oldStore) {
                $oldStore->decrement('quantity', $supply->quantity);
            }

            $newStore = Store::firstOrCreate(
                ['product_name' => $validated['product_name']],
                ['quantity' => 0, 'unit' => $validated['unit'], 'description' => $validated['description']]
            );
            $newStore->increment('quantity', $validated['quantity']);
        }

        // Now update the supply
        $supply->update($validated);

        flash()->option('position', 'bottom-center')->success("Supply Update Successfully");
        return Inertia::location('/supplies');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supply $supply)
    {
        // Decrement quantity from store
    $store = Store::where('product_name', $supply->product_name)->first();

    if ($store) {
        $store->decrement('quantity', $supply->quantity);

        // Optional: delete the store record if quantity is now 0
        if ($store->quantity <= 0) {
            $store->delete();
        }
    }

    $supply->delete();

    flash()->option('position', 'bottom-center')->error("Supply Deleted Successfully");

        return Inertia::location('/supplies');

    }

    public function print($id)
    {
        $supply = Supply::with(['supplier', 'recipient'])->findOrFail($id);
        return view('supplies.print', compact('supply')); // If stored in resources/views/supplies/print.blade.php

    }

    public function printAll(Request $request)
{

    $locale = $request->get('lang', 'en'); // Get it from the URL
    App::setLocale($locale); // Tell Laravel which language to use

    $supplies = Supply::with(['supplier', 'recipient'])->latest()->paginate(10);
    return view('supplies.print-all', compact('supplies'));
}

}
