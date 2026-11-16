<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'alamat',
        'kota',
        'provinsi',
        'latitude',
        'longitude',
        'jenis',
        'jam_operasional',
        'harga_min',
        'harga_max',
        'rating',
        'jumlah_ulasan',
        'image',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'harga_min' => 'decimal:2',
        'harga_max' => 'decimal:2',
        'rating' => 'decimal:1',
        'jumlah_ulasan' => 'integer',
    ];
}
