<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_trip',
        'wilayah_tujuan',
        'tanggal_mulai',
        'durasi_hari',
        'jumlah_peserta',
        'anggaran',
        'preferensi',
        'total_estimasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'durasi_hari' => 'integer',
        'jumlah_peserta' => 'integer',
        'anggaran' => 'decimal:2',
        'preferensi' => 'array',
        'total_estimasi' => 'decimal:2',
    ];

    protected $appends = ['sisa_anggaran'];

    protected function sisaAnggaran(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->anggaran - (float) $this->total_estimasi, 2)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itineraryItems(): HasMany
    {
        return $this->hasMany(ItineraryItem::class)
            ->orderBy('hari_ke')
            ->orderBy('urutan_kunjungan');
    }

    public function costs(): HasMany
    {
        return $this->hasMany(TripCost::class)->orderBy('id');
    }
}
