<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review for a destination.
     */
    public function store(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Pilih rating bintang 1-5.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'komentar.max' => 'Komentar ulasan maksimal 1000 karakter.',
        ]);

        // Prevent duplicate review per user
        $existing = Review::where('user_id', Auth::id())
            ->where('destination_id', $destination->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk destinasi ini.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'destination_id' => $destination->id,
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim.');
    }

    /**
     * Delete user's review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}