<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'destination_id',
        'hari_ke',
        'urutan_kunjungan',
        'catatan',
    ];

    protected $casts = [
        'hari_ke' => 'integer',
        'urutan_kunjungan' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(TripCost::class);
    }
}
