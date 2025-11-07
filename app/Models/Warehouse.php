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

    // link user to warehouse
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
