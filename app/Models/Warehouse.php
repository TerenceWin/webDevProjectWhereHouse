<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_name',
        'user_id',
    ];

    // Original creator - keep for backward compatibility
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'warehouse_user')->withTimestamps();
    }

    // A warehouse has many sections
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // A warehouse has many products through sections
    public function products()
    {
        return $this->hasManyThrough(Product::class, Section::class);
    }
    
    // Helper method: Check if user has access
    public function hasAccess($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }
}