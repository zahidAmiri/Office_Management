<?php

namespace App\Http\Controllers;

use App\Models\UsedStore;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsedStoreController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/UsedStore/Index', [
            'usedItems' => UsedStore::orderBy('product_name')->get(),
        ]);
    }
}
