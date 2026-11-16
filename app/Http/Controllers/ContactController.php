<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:191'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactInquiry::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for reaching out. Our concierge team will respond within 1–2 business days.',
            ], 201);
        }

        return back()->with('status', 'Thank you for reaching out. Our concierge team will respond within 1–2 business days.');
    }
}
