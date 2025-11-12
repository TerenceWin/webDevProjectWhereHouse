<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request, $warehouseId)
    {
        try {
            $request->validate([
                'section_name' => 'required|string|max:255',
                'grid_x' => 'nullable|integer|min:0|max:29',  // ADD THIS (0-29 for 30 columns)
                'grid_y' => 'nullable|integer|min:0|max:19',  // ADD THIS (0-19 for 20 rows)
            ]);

            $warehouse = auth()->user()->warehouses()->findOrFail($warehouseId);

            $section = Section::create([
                'section_name' => $request->section_name,
                'user_id' => auth()->id(),
                'warehouse_id' => $warehouse->id,
                'grid_x' => $request->grid_x,  // ADD THIS
                'grid_y' => $request->grid_y,  // ADD THIS
            ]);

            return response()->json([
                'success' => true,
                'section' => $section,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating section: ' . $e->getMessage()
            ], 500);
        }
    }

    // List all sections in a warehouse
    public function index($warehouseId)
    {
        // Find the warehouse by ID for the logged-in user
        $warehouse = auth()->user()->warehouses()->findOrFail($warehouseId);

        // Get all sections for this warehouse
        $sections = $warehouse->sections;

        return response()->json([
            'sections' => $sections,
        ]);
    }

    // Delete a section from a warehouse
    public function destroy($warehouseId, $sectionId)
    {
        // Find the warehouse and section for the logged-in user
        $warehouse = auth()->user()->warehouses()->findOrFail($warehouseId);
        $section = $warehouse->sections()->findOrFail($sectionId);

        // Delete the section
        $section->delete();

        return response()->json(['success' => true]);
    }

    // Add this method to SectionController
    public function updatePosition(Request $request, $warehouseId, $sectionId)
    {
        try {
            $request->validate([
                'grid_x' => 'required|integer|min:0|max:29',
                'grid_y' => 'required|integer|min:0|max:19',
            ]);

            $warehouse = auth()->user()->warehouses()->findOrFail($warehouseId);
            $section = $warehouse->sections()->findOrFail($sectionId);

            $section->update([
                'grid_x' => $request->grid_x,
                'grid_y' => $request->grid_y,
            ]);

            return response()->json([
                'success' => true,
                'section' => $section,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating position: ' . $e->getMessage()
            ], 500);
        }
    }
}
