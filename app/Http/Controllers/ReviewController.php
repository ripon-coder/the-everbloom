<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'author' => ['required', 'string', 'max:120'],
            'author_email' => ['nullable', 'email', 'max:191'],
            'variant' => ['nullable', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'headline' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::guard('web')->id(),
            'author' => $validated['author'],
            'author_email' => $validated['author_email'] ?? Auth::guard('web')->user()?->email,
            'variant' => $validated['variant'] ?? ($product->sizes[0] ?? null),
            'rating' => (int) $validated['rating'],
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'verified' => Auth::guard('web')->check(),
            'status' => 'Pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your review has been submitted for moderation.',
                'review' => $review->toArray(),
            ], 201);
        }

        return back()->with('status', 'Thank you! Your review has been submitted for moderation.');
    }
}
