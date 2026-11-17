<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
            'headline' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $user = Auth::guard('web')->user();
        $email = isset($validated['author_email']) && trim($validated['author_email']) !== ''
            ? trim(strtolower($validated['author_email']))
            : ($user?->email ? trim(strtolower($user->email)) : null);

        // One review per user/email per product
        $alreadyReviewed = Review::where('product_id', $product->id)
            ->where(function ($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                }
                if ($email) {
                    $method = $user ? 'orWhereRaw' : 'whereRaw';
                    $q->{$method}('LOWER(author_email) = ?', [$email]);
                }
            })
            ->when(! $user && ! $email, fn ($q) => $q->whereRaw('1 = 0'))
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages([
                'body' => ['You have already submitted a review for this product.'],
            ]);
        }

        // Verified buyer: customer has a non-cancelled order containing this product
        $isVerifiedBuyer = false;
        if ($user || $email) {
            $isVerifiedBuyer = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($q) use ($user, $email) {
                    $q->where('status', '!=', 'Cancelled');
                    $q->where(function ($sub) use ($user, $email) {
                        if ($user) {
                            $sub->where('user_id', $user->id);
                        }
                        if ($email) {
                            $method = $user ? 'orWhereRaw' : 'whereRaw';
                            $sub->{$method}('LOWER(customer_email) = ?', [$email]);
                        }
                    });
                })
                ->exists();
        }

        $headline = trim((string) ($validated['headline'] ?? ''));
        if ($headline === '') {
            $headline = mb_substr(trim($validated['body']), 0, 60);
        }

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'author' => trim($validated['author']),
            'author_email' => $email,
            'variant' => $validated['variant'] ?? ($product->sizes[0] ?? null),
            'rating' => (int) $validated['rating'],
            'headline' => $headline,
            'body' => trim($validated['body']),
            'verified' => $isVerifiedBuyer,
            'status' => Review::STATUS_PENDING,
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
