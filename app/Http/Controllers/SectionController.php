<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request, $warehouseId)
    {
        // Validate the section name
        $request->validate([
            'section_name' => 'required|string|max:255',
        ]);

        // Find the warehouse by ID for the logged-in user
        $warehouse = auth()->user()->warehouses()->findOrFail($warehouseId);

        // Create a new section - Laravel will auto-set warehouse_id
        $section = Section::create([
            'section_name' => $request->section_name,
            'user_id' => auth()->id(),
            'warehouse_id' => $warehouse->id,
        ]);

        return response()->json([
            'success' => true,
            'section' => $section,
        ]);
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
}
