<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Create a new product in a section
    public function store(Request $request, $warehouseId, $sectionId)
    {
        try {
            $request->validate([
                'product_name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:0',
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

            // Verify the section belongs to this warehouse
            $section = $warehouse->sections()->findOrFail($sectionId);

            $product = Product::create([
                'product_name' => $request->product_name,
                'sku' => $request->sku,
                'quantity' => $request->quantity ?? 0,
                'user_id' => auth()->id(),
                'section_id' => $section->id,
            ]);

            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    // List all products in a section
    public function index($warehouseId, $sectionId)
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
            $products = $section->products;

            return response()->json([
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading products'
            ], 500);
        }
    }

    // Delete a product from a section
    public function destroy($warehouseId, $sectionId, $productId)
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
            $product = $section->products()->findOrFail($productId);
            $product->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product'
            ], 500);
        }
    }

    // Show a specific product (optional - for future use)
    public function show($warehouseId, $sectionId, $productId)
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
            $product = $section->products()->findOrFail($productId);

            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading product'
            ], 500);
        }
    }

    // Update a product (optional - for future use)
    public function update(Request $request, $warehouseId, $sectionId, $productId)
    {
        try {
            $request->validate([
                'product_name' => 'sometimes|required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:0',
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
            $product = $section->products()->findOrFail($productId);
            
            $product->update($request->only(['product_name', 'sku', 'quantity']));

            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }
}
