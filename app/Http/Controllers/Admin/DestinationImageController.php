<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDestinationImageRequest;
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
    public function store(StoreDestinationImageRequest $request): RedirectResponse
    {
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
     * Update caption / metadata of an image (FR-11).
     */
    public function update(Request $request, DestinationImage $destinationImage): RedirectResponse
    {
        $validated = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $destinationImage->update([
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Keterangan foto berhasil diperbarui.');
    }

    /**
     * Update display order of an image.
     */
    public function updateOrder(Request $request, DestinationImage $destinationImage): RedirectResponse
    {
        $validated = $request->validate([
            'urutan_tampil' => ['required', 'integer', 'min:1'],
        ]);

        $destinationImage->update([
            'urutan_tampil' => (int) $validated['urutan_tampil'],
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
