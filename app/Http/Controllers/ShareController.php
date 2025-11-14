<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    // Share warehouse with another user
    public function share(Request $request, $warehouseId)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            // Find the warehouse
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if current user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this warehouse'
                ], 403);
            }

            // Find the user to share with
            $userToShare = User::where('email', $request->email)->first();
            
            // Don't allow sharing with self
            if ($userToShare->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot share warehouse with yourself'
                ], 400);
            }

            // Check if already shared
            if ($warehouse->hasAccess($userToShare->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse already shared with this user'
                ], 400);
            }

            // Share the warehouse
            $warehouse->users()->attach($userToShare->id);

            return response()->json([
                'success' => true,
                'message' => 'Warehouse shared successfully',
                'user' => [
                    'id' => $userToShare->id,
                    'name' => $userToShare->name,
                    'email' => $userToShare->email,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sharing warehouse: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get list of users warehouse is shared with
    public function listShared($warehouseId)
    {
        try {
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $sharedUsers = $warehouse->users()
                ->select('users.id', 'users.name', 'users.email')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $sharedUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading shared users'
            ], 500);
        }
    }

    public function unshare(Request $request, $warehouseId, $userId)
    {
        try {
            $warehouse = Warehouse::findOrFail($warehouseId);
            
            // Check if current user has access
            if (!$warehouse->hasAccess(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Don't allow removing yourself
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove yourself. Delete the warehouse instead.'
                ], 400);
            }

            // Don't allow removing the warehouse creator (owner)
            if ($userId == $warehouse->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove the warehouse owner'
                ], 400);
            }

            $warehouse->users()->detach($userId);

            return response()->json([
                'success' => true,
                'message' => 'User removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing user: ' . $e->getMessage()
            ], 500);
        }
    }
}
