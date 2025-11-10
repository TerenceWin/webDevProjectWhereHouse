<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_name',
        'user_id',
        'warehouse_id',
    ];

    // A section belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A section belongs to a warehouse
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
