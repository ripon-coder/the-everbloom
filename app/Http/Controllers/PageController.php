<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->first();

        if ($page && trim((string) $page->content) !== '') {
            return view('frontend.pages.cms', compact('page'));
        }

        $fallbackViews = [
            'about' => 'frontend.pages.about',
            'privacy-policy' => 'frontend.pages.privacy-policy',
            'terms-and-conditions' => 'frontend.pages.terms-and-conditions',
            'shipping-and-returns' => 'frontend.pages.shipping-and-returns',
        ];

        if (isset($fallbackViews[$slug])) {
            return view($fallbackViews[$slug], ['cmsPage' => $page]);
        }

        abort(404);
    }

    public function faq(): View
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pages.faq', compact('faqs'));
    }
}

