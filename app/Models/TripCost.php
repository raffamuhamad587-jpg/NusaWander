<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripCost extends Model
{
    protected $fillable = [
        'trip_id',
        'harga_per_orang',
        'harga_per_kelompok',
        'keterangan',
    ];

    protected $casts = [
        'harga_per_orang' => 'decimal:2',
        'harga_per_kelompok' => 'decimal:2',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}