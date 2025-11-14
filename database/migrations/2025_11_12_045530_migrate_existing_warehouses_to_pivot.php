<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing warehouses
        $warehouses = DB::table('warehouses')->get();
        
        // Add each warehouse owner to the pivot table
        foreach ($warehouses as $warehouse) {
            // Check if entry already exists to avoid duplicates
            $exists = DB::table('warehouse_user')
                ->where('warehouse_id', $warehouse->id)
                ->where('user_id', $warehouse->user_id)
                ->exists();
                
            if (!$exists) {
                DB::table('warehouse_user')->insert([
                    'warehouse_id' => $warehouse->id,
                    'user_id' => $warehouse->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('warehouse_user')->truncate();
    }
};