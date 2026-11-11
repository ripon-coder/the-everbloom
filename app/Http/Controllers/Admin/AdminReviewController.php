<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        // BACKEND: Review::with(['product', 'user'])->latest()->paginate(15)
        $reviews = [
            [
                'id' => 1,
                'author' => 'Eleanor Vance',
                'avatar' => 'admin/assets/images/users/avatar-2.jpg',
                'product_name' => 'Botanical Radiance Serum',
                'rating' => 5,
                'headline' => 'Transformative morning glow',
                'comment' => 'The jojoba and rosehip blend absorbs effortlessly without any tacky residue. My skin feels nourished through all winter weather.',
                'date' => 'Nov 09, 2026',
                'status' => 'Approved',
                'status_badge' => 'success',
            ],
            [
                'id' => 2,
                'author' => 'Sophia Laurent',
                'avatar' => 'admin/assets/images/users/avatar-3.jpg',
                'product_name' => 'Elderberry Gentle Cleansing Milk',
                'rating' => 5,
                'headline' => 'Gentlest cleanser I have ever used',
                'comment' => 'Melted away light mineral makeup while leaving my barrier calm and moisturized. No tightness whatsoever.',
                'date' => 'Nov 07, 2026',
                'status' => 'Approved',
                'status_badge' => 'success',
            ],
            [
                'id' => 3,
                'author' => 'Emma Chen',
                'avatar' => 'admin/assets/images/users/avatar-4.jpg',
                'product_name' => 'Rosewater Hydrating Mist',
                'rating' => 4,
                'headline' => 'Beautiful delicate floral aroma',
                'comment' => 'The fine micro-mist is super refreshing mid-afternoon at my desk. Subtle natural scent from pure steam distillation.',
                'date' => 'Nov 05, 2026',
                'status' => 'Approved',
                'status_badge' => 'success',
            ],
            [
                'id' => 4,
                'author' => 'Lucas Wright',
                'avatar' => 'admin/assets/images/users/avatar-5.jpg',
                'product_name' => 'Whipped Calendula Night Cream',
                'rating' => 5,
                'headline' => 'Rich barrier hydration',
                'comment' => 'A pea-sized amount covers the whole face. Woke up with deeply hydrated skin after our dry central heating nights.',
                'date' => 'Nov 01, 2026',
                'status' => 'Pending',
                'status_badge' => 'warning',
            ],
        ];

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleStatus(Request $request, $id)
    {
        // BACKEND: Toggle approval status
        return redirect()->back()->with('success', 'Review status updated.');
    }
}
