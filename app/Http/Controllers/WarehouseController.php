<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        // Get all warehouses the user has access to (owned + shared)
        $warehouses = auth()->user()->warehouses()->get(['warehouses.id', 'warehouses.warehouse_name', 'warehouses.user_id']);

        // If it's an AJAX request (or JSON expected), return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'warehouses' => $warehouses
            ]);
        }

        // Otherwise, render the dashboard view
        return view('dashboard', compact('warehouses'));
    }

    // Create a new warehouse via AJAX
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:255',
        ]);

        // Create the warehouse
        $warehouse = Warehouse::create([
            'warehouse_name' => $request->warehouse_name,
            'user_id' => auth()->id(),
        ]);
        
        // Attach creator to pivot table
        $warehouse->users()->attach(auth()->id());

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        
        // Check if user has access
        if (!$warehouse->hasAccess(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $warehouse->delete();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        // Fetch the warehouse by its ID
        $warehouse = Warehouse::findOrFail($id);
        
        // Check if user has access
        if (!$warehouse->hasAccess(auth()->id())) {
            abort(403, 'You do not have access to this warehouse');
        }

        // Return the warehouse details view and pass the warehouse data
        return view('grid', compact('warehouse'));
    }

    // Add this method to your WarehouseController class
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'warehouse_name' => 'required|string|max:255',
            ]);

            $warehouse = Warehouse::findOrFail($id);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $warehouse->update([
                'warehouse_name' => $request->warehouse_name,
            ]);

            return response()->json([
                'success' => true,
                'warehouse' => $warehouse
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating warehouse: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
