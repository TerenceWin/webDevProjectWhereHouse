<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'sku',
        'quantity',
        'user_id',
        'section_id',
    ];

    // A product belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A product belongs to a section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // A product belongs to a section
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}