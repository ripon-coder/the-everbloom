<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SlugRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase17SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_sitemap_and_robots_render_valid_seo_output(): void
    {
        $product = Product::where('is_active', true)->firstOrFail();
        $category = Category::where('is_active', true)->firstOrFail();
        $brand = Brand::where('is_active', true)->first();

        $sitemap = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $sitemap->assertSee(route('shop.product', $product->slug), false);
        $sitemap->assertSee(route('shop.category', $category->slug), false);
        if ($brand) {
            $sitemap->assertSee(route('shop.brand', $brand->slug), false);
        }

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap:');
    }

    public function test_product_json_ld_uses_real_brand_and_review_count(): void
    {
        $product = Product::where('is_active', true)->firstOrFail();
        $product->update([
            'brand' => 'Botanica Dhaka',
            'review_count' => 0,
        ]);

        $res = $this->get(route('shop.product', $product->slug))->assertOk();
        $res->assertSee('Botanica Dhaka');
        $res->assertDontSee('"aggregateRating"', false);

        $product->update(['review_count' => 4, 'rating' => 4.75]);
        $this->get(route('shop.product', $product->slug))
            ->assertOk()
            ->assertSee('"aggregateRating"', false);
    }

    public function test_admin_seo_overview_and_slug_redirects_work(): void
    {
        $admin = Admin::firstOrFail();
        $product = Product::where('is_active', true)->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.seo.index'))
            ->assertOk()
            ->assertSee('SEO Overview');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.seo.redirects.store'), [
                'old_path' => '/shop/product/legacy-rose-serum',
                'new_path' => '/shop/product/' . $product->slug,
                'status_code' => '301',
            ])
            ->assertRedirect();

        $this->get('/shop/product/legacy-rose-serum')
            ->assertRedirect('/shop/product/' . $product->slug);

        $redirect = SlugRedirect::where('old_path', '/shop/product/legacy-rose-serum')->firstOrFail();
        $this->assertEquals(1, $redirect->hits);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.seo.sitemap.flush'))
            ->assertRedirect();

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.seo.redirects.destroy', $redirect))
            ->assertRedirect();

        $this->assertDatabaseMissing('slug_redirects', ['id' => $redirect->id]);
    }
}

