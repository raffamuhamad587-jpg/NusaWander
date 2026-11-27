<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'itinerary_item_id',
        'nama_biaya',
        'jenis_biaya',
        'dasar_perhitungan',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function itineraryItem(): BelongsTo
    {
        return $this->belongsTo(ItineraryItem::class);
    }
}
