<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4CategoryStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_navigation_and_shop_sidebar_reflect_active_db_categories(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'name' => 'Botanical Mists',
            'short_name' => 'Mists',
            'slug' => 'botanical-mists',
            'sort_order' => 0,
            'is_active' => '1',
        ])->assertRedirect(route('admin.categories.index'));

        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'name' => 'Hidden Archive',
            'short_name' => 'Hidden',
            'slug' => 'hidden-archive',
            'sort_order' => 99,
            'is_active' => '0',
        ])->assertRedirect(route('admin.categories.index'));

        $this->get('/shop')
            ->assertOk()
            ->assertSee('Botanical Mists')
            ->assertSee('/shop/category/botanical-mists')
            ->assertDontSee('Hidden Archive');

        // Inactive category page returns 404
        $this->get('/shop/category/hidden-archive')->assertNotFound();
    }

    public function test_category_page_renders_category_image_and_db_counts(): void
    {
        $this->seed();

        $category = Category::where('is_active', true)->firstOrFail();
        $category->update([
            'image' => 'categories/test-hero.jpg',
            'image_source' => 'local',
        ]);

        $totalActive = Product::where('is_active', true)->count();

        $this->get("/shop/category/{$category->slug}")
            ->assertOk()
            ->assertSee('categories/test-hero.jpg')
            ->assertSee("({$totalActive})");
    }
}

