<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_destination_without_photos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'nama_kategori' => 'Alam',
            'deskripsi' => 'Wisata alam',
        ]);

        $response = $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'nama' => 'Candi Borobudur',
            'deskripsi' => 'Candi Buddha terbesar',
            'alamat' => 'Magelang',
            'kota' => 'Magelang',
            'jam_operasional' => '08:00 - 17:00',
            'estimasi_harga' => 50000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/destinations');
        $this->assertDatabaseHas('destinations', ['nama' => 'Candi Borobudur']);
    }

    public function test_admin_can_store_destination_with_null_price_as_uncalculated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['nama_kategori' => 'Budaya', 'deskripsi' => 'Budaya']);

        $response = $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'nama' => 'Desa Adat Penglipuran',
            'deskripsi' => 'Desa adat tradisional',
            'alamat' => 'Bangli, Bali',
            'kota' => 'Bangli',
            'estimasi_harga' => null,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/destinations');
        $this->assertDatabaseHas('destinations', [
            'nama' => 'Desa Adat Penglipuran',
            'estimasi_harga' => null,
        ]);
    }

    public function test_destination_requires_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/destinations', []);

        $response->assertSessionHasErrors(['category_id', 'nama', 'deskripsi', 'alamat', 'kota']);
    }
}
