<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Return JSON list of warehouses for AJAX
    public function index(Request $request)
    {
        $warehouses = auth()->user()->warehouses()->get(['id', 'warehouse_name']);

        // If it's an AJAX request (or JSON expected), return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'warehouses' => $warehouses
            ]);
        }

        // Otherwise, render a Blade view (optional)
        return view('dashboard', compact('warehouses'));
    }

    // Create a new warehouse via AJAX
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:255',
        ]);

        $warehouse = auth()->user()->warehouses()->create([
            'warehouse_name' => $request->warehouse_name,
        ]);

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Find the warehouse by ID for the logged-in user
        $warehouse = auth()->user()->warehouses()->findOrFail($id);

        // Delete the warehouse
        $warehouse->delete();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        // Fetch the warehouse by its ID for the logged-in user
        $warehouse = auth()->user()->warehouses()->findOrFail($id);

        // Return the warehouse details view and pass the warehouse data
        return view('grid', compact('warehouse'));
    }
    
}
