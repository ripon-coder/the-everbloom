<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $xml = Cache::remember('sitemap_xml', 3600, function () {
            $urls = [];
            $urls[] = ['loc' => url('/'), 'priority' => '1.0'];
            $urls[] = ['loc' => route('shop.index'), 'priority' => '0.9'];
            $urls[] = ['loc' => route('flash-sales.index'), 'priority' => '0.8'];

            foreach (['/about', '/contact', '/faq', '/privacy-policy', '/terms-and-conditions', '/shipping-and-returns'] as $staticPath) {
                $urls[] = ['loc' => url($staticPath), 'priority' => '0.5'];
            }

            foreach (Category::where('is_active', true)->select(['slug', 'updated_at'])->get() as $cat) {
                $urls[] = [
                    'loc' => route('shop.category', $cat->slug),
                    'lastmod' => $cat->updated_at?->toAtomString(),
                    'priority' => '0.8',
                ];
            }

            foreach (Brand::where('is_active', true)->select(['slug', 'updated_at'])->get() as $brand) {
                $urls[] = [
                    'loc' => route('shop.brand', $brand->slug),
                    'lastmod' => $brand->updated_at?->toAtomString(),
                    'priority' => '0.7',
                ];
            }

            foreach (Product::where('is_active', true)->select(['slug', 'updated_at'])->get() as $product) {
                $urls[] = [
                    'loc' => route('shop.product', $product->slug),
                    'lastmod' => $product->updated_at?->toAtomString(),
                    'priority' => '0.9',
                ];
            }

            foreach (Page::where('is_active', true)->select(['slug', 'updated_at'])->get() as $page) {
                $urls[] = [
                    'loc' => route('cms.page', $page->slug),
                    'lastmod' => $page->updated_at?->toAtomString(),
                    'priority' => '0.6',
                ];
            }

            $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
            foreach ($urls as $u) {
                $lines[] = '  <url>';
                $lines[] = '    <loc>' . e($u['loc']) . '</loc>';
                if (! empty($u['lastmod'])) {
                    $lines[] = '    <lastmod>' . e($u['lastmod']) . '</lastmod>';
                }
                $lines[] = '    <priority>' . e($u['priority']) . '</priority>';
                $lines[] = '  </url>';
            }
            $lines[] = '</urlset>';

            return implode("\n", $lines);
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /checkout',
            'Disallow: /account',
            'Disallow: /cart',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}

