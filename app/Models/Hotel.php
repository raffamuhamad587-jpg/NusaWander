<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
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
        'tipe',
        'bintang',
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
        'bintang' => 'integer',
        'jumlah_ulasan' => 'integer',
    ];
}
