<?php

namespace App\Http\Controllers;

use App\Models\ExperimentalEquipmentsReturn;
use App\Models\ExperimentalEquipmentsTransfer;
use App\Models\Store;
use App\Models\UsedStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperimentalEquipmentsReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = ExperimentalEquipmentsTransfer::with(['department', 'recipient', 'equipmentReturn'])->get();

        return inertia('Admin/ExperimentalReturn/Index', [
            'transfers' => $transfers
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
    $data = $request->validate([
        'transfer_id'        => 'required|exists:experimental_equipments_transfers,id',
        'return_date'        => 'required|date',
        'returned_condition' => 'required|integer|between:1,5',
        'description'        => 'nullable|string',
    ]);

    $transfer = ExperimentalEquipmentsTransfer::findOrFail($data['transfer_id']);
    if ($transfer->status === 'Returned') {
        return back()->withErrors(['transfer_id'=>'Already returned']);
    }

    DB::transaction(function () use ($transfer,$data) {
        /* add / merge row in used_stores */
        UsedStore::updateOrCreate(
            [
                'product_name' => $transfer->product_name,
                'unit'         => $transfer->unit,
                'condition'    => $data['returned_condition'],
            ],
            [
                'quantity'    => DB::raw('quantity + '.$transfer->quantity),
                'description' => $data['description'] ?? null,
            ]
        );

        $transfer->update(['status'=>'Returned']);

        ExperimentalEquipmentsReturn::create($data);
    });

    return back()->with('success','Returned & stored');
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
