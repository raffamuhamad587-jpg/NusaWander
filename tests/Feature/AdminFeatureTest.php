<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_wisatawan_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'wisatawan',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'nama_kategori' => 'Petualangan',
            'deskripsi' => 'Kategori petualangan seru',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'nama_kategori' => 'Petualangan',
        ]);
    }

    public function test_cannot_delete_category_with_destinations(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Bahari',
            'deskripsi' => 'Wisata laut',
        ]);

        Destination::create([
            'category_id' => $category->id,
            'nama' => 'Pantai Parangtritis',
            'deskripsi' => 'Pantai di selatan Yogyakarta',
            'alamat' => 'Kretek, Bantul',
            'kota' => 'Bantul',
            'estimasi_harga' => 10000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_toggle_destination_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Bahari',
            'deskripsi' => 'Wisata laut',
        ]);

        $dest = Destination::create([
            'category_id' => $category->id,
            'nama' => 'Pantai Parangtritis',
            'deskripsi' => 'Pantai di selatan Yogyakarta',
            'alamat' => 'Kretek, Bantul',
            'kota' => 'Bantul',
            'estimasi_harga' => 10000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/destinations/{$dest->id}/toggle-status");
        $response->assertRedirect();
        $this->assertDatabaseHas('destinations', [
            'id' => $dest->id,
            'status' => 'nonaktif',
        ]);
    }
}