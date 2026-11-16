<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'destination_id',
        'judul',
        'deskripsi',
        'total_estimasi_harga',
        'status',
    ];

    protected $casts = [
        'total_estimasi_harga' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('urutan');
    }

    public function costs(): HasMany
    {
        return $this->hasMany(TripCost::class);
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(ItineraryItem::class);
    }
}