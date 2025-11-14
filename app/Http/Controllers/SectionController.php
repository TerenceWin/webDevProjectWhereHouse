<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // Create a new section in a warehouse
    public function store(Request $request, $warehouseId)
    {
        try {
            $request->validate([
                'section_name' => 'required|string|max:255',
                'grid_x' => 'nullable|integer|min:0|max:29',
                'grid_y' => 'nullable|integer|min:0|max:19',
            ]);

            // Find the warehouse
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $section = Section::create([
                'section_name' => $request->section_name,
                'user_id' => auth()->id(),
                'warehouse_id' => $warehouse->id,
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
                'message' => 'Error creating section: ' . $e->getMessage()
            ], 500);
        }
    }

    // List all sections in a warehouse
    public function index($warehouseId)
    {
        try {
            // Find the warehouse
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Get all sections for this warehouse
            $sections = $warehouse->sections;

            return response()->json([
                'sections' => $sections,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading sections'
            ], 500);
        }
    }

    // Delete a section from a warehouse
    public function destroy($warehouseId, $sectionId)
    {
        try {
            // Find the warehouse
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $section = $warehouse->sections()->findOrFail($sectionId);

            // Delete the section
            $section->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting section'
            ], 500);
        }
    }

    // Update section position
    public function updatePosition(Request $request, $warehouseId, $sectionId)
    {
        try {
            $request->validate([
                'grid_x' => 'required|integer|min:0|max:29',
                'grid_y' => 'required|integer|min:0|max:19',
            ]);

            // Find the warehouse
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

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

    // Add this method to your SectionController class
    public function update(Request $request, $warehouseId, $sectionId)
    {
        try {
            $request->validate([
                'section_name' => 'required|string|max:255',
            ]);

            $warehouse = Warehouse::findOrFail($warehouseId);
            
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $section = $warehouse->sections()->findOrFail($sectionId);
            
            $section->update([
                'section_name' => $request->section_name,
            ]);

            return response()->json([
                'success' => true,
                'section' => $section,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating section: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
