<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationImageController extends Controller
{
    /**
     * Store new images for a destination.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran file gambar maksimal 2MB.',
        ]);

        $destination = Destination::findOrFail($request->destination_id);
        $currentMaxOrder = $destination->images()->max('urutan_tampil') ?? 0;

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('destinations', 'public');
                $currentMaxOrder++;
                DestinationImage::create([
                    'destination_id' => $destination->id,
                    'url_gambar' => '/storage/' . $path,
                    'keterangan' => $request->keterangan,
                    'urutan_tampil' => $currentMaxOrder,
                ]);
            }
        }

        return back()->with('success', 'Foto destinasi berhasil diunggah.');
    }

    /**
     * Update order of an image.
     */
    public function updateOrder(Request $request, DestinationImage $destinationImage): RedirectResponse
    {
        $request->validate([
            'urutan_tampil' => 'required|integer|min:1',
        ]);

        $destinationImage->update([
            'urutan_tampil' => $request->urutan_tampil,
        ]);

        return back()->with('success', 'Urutan foto berhasil diperbarui.');
    }

    /**
     * Remove the specified image.
     */
    public function destroy(DestinationImage $destinationImage): RedirectResponse
    {
        $relativePath = str_replace('/storage/', '', $destinationImage->url_gambar);
        Storage::disk('public')->delete($relativePath);

        $destinationImage->delete();

        return back()->with('success', 'Foto destinasi berhasil dihapus.');
    }
}
