<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\ExperimentalEquipmentsTransfer;
use App\Models\Store;
use App\Models\UsedStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExperimentalEquipmentsTransferController extends Controller
{
     /* ----------  LIST  ---------- */
     public function index()
    {
        return Inertia::render('Admin/ExperimentalTransfer/Index', [
            'transfers'   => ExperimentalEquipmentsTransfer::with(['recipient','distributor','equipmentReturn'])->get(),
            'departments' => Department::all(),
            'employees'   => Employee::all(),
            'storeItems'  => Store::where('quantity','>',0)->get(),
            'usedItems'   => UsedStore::whereBetween('condition',[2,4])
                                       ->where('quantity','>',0)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    //     $departments = Department::all();
    // $storeItems = Store::all(); // Assuming you have a Store model
    // return Inertia::render('ExperimentalTransfers/Create', compact('departments', 'storeItems'));
    }


     /* =======================================================
     |  STORE  (create a transfer & decrement stock)
     =======================================================*/
     public function store(Request $request)
     {
        $data = $request->validate([
            'source'            => 'required|in:store,used',
            'product_name'      => 'required|string',
            'product_type'      => 'nullable|string',
            'unit'              => 'required|string',
            'quantity'          => 'required|integer|min:1',
            'department_id'     => 'required|exists:departments,id',
            'recipient_id'      => 'required|exists:employees,id',
            'distributed_by'    => 'required|exists:employees,id',
            'date'              => 'required|date',
            'product_condition' => 'required_if:source,store|integer|between:1,5',
            'condition'         => 'required_if:source,used|integer|between:2,4',
            'description'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {

            if ($data['source'] === 'store') {
                $stock = Store::where('product_name',$data['product_name'])
                              ->where('unit',$data['unit'])
                              ->lockForUpdate()->first();
                $condition = $data['product_condition'];
            } else {
                $stock = UsedStore::where('product_name',$data['product_name'])
                                  ->where('unit',$data['unit'])
                                  ->where('condition',$data['condition'])
                                  ->lockForUpdate()->first();
                $condition = $data['condition'];
            }

            if (!$stock || $stock->quantity < $data['quantity']) {
                abort(422,'Not enough stock in selected level.');
            }

            $stock->decrement('quantity', $data['quantity']);

            ExperimentalEquipmentsTransfer::create([
                'product_name'      => $data['product_name'],
                'product_type'      => $data['product_type'],
                'unit'              => $data['unit'],
                'quantity'          => $data['quantity'],
                'department_id'     => $data['department_id'],
                'recipient_id'      => $data['recipient_id'],
                'distributed_by'    => $data['distributed_by'],
                'date'              => $data['date'],
                'product_condition' => $condition,
                'source'            => $data['source'],
                'description'       => $data['description'],
            ]);
        });

        return back()->with('success','Distributed');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
