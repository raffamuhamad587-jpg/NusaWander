<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicDestinationFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_filters_by_search_category_and_kota(): void
    {
        $alam = Category::create(['nama_kategori' => 'Alam', 'deskripsi' => 'Wisata alam']);
        $sejarah = Category::create(['nama_kategori' => 'Sejarah', 'deskripsi' => 'Wisata sejarah']);

        Destination::create([
            'category_id' => $alam->id,
            'nama' => 'Pantai Parangtritis',
            'deskripsi' => 'Pantai di Yogyakarta',
            'alamat' => 'Bantul, Yogyakarta',
            'kota' => 'Yogyakarta',
            'estimasi_harga' => 15000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        Destination::create([
            'category_id' => $sejarah->id,
            'nama' => 'Candi Borobudur',
            'deskripsi' => 'Candi bersejarah',
            'alamat' => 'Magelang, Jawa Tengah',
            'kota' => 'Magelang',
            'estimasi_harga' => 50000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        Destination::create([
            'category_id' => $alam->id,
            'nama' => 'Destinasi Nonaktif',
            'deskripsi' => 'Tidak tayang',
            'alamat' => 'Yogyakarta',
            'kota' => 'Yogyakarta',
            'estimasi_harga' => 10000,
            'jenis_harga' => 'per_orang',
            'status' => 'nonaktif',
        ]);

        // Filter by kota + category
        $this->get("/destinasi?kota=Yogyakarta&category_id={$alam->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Destinasi/Index')
                ->has('destinations.data', 1)
                ->where('destinations.data.0.nama', 'Pantai Parangtritis')
            );

        // Filter by search keyword
        $this->get('/destinasi?search=Borobudur')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Destinasi/Index')
                ->has('destinations.data', 1)
                ->where('destinations.data.0.nama', 'Candi Borobudur')
            );
    }
}
