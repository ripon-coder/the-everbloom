<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase5BrandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_brands_and_assigned_brand_cannot_be_deleted(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $category = Category::firstOrFail();

        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $this->actingAs($admin, 'admin')->post('/admin/brands', [
            'name' => 'Botanical Atelier',
            'slug' => 'botanical-atelier',
            'description' => 'Small-batch cold-pressed botanicals.',
            'sort_order' => 1,
            'is_active' => '1',
            'meta_title' => 'Botanical Atelier SEO',
            'meta_description' => 'Atelier brand SEO.',
            'logo' => UploadedFile::fake()->image('atelier.jpg', 400, 400),
        ])->assertRedirect(route('admin.brands.index'));

        $brand = Brand::where('slug', 'botanical-atelier')->firstOrFail();
        $this->assertNotNull($brand->logo);

        // Create product linked to brand_id
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Atelier Face Oil',
            'category_slug' => $category->slug,
            'brand_id' => $brand->id,
            'sku' => 'EB-BR-01',
            'price' => 980,
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'EB-BR-01')->firstOrFail();
        $this->assertSame($brand->id, $product->brand_id);
        $this->assertSame('Botanical Atelier', $product->brand);

        // Brand with products cannot be deleted
        $this->actingAs($admin, 'admin')
            ->from('/admin/brands')
            ->delete("/admin/brands/{$brand->id}")
            ->assertRedirect('/admin/brands')
            ->assertSessionHasErrors('brand');

        $this->assertDatabaseHas('brands', ['id' => $brand->id]);

        // Storefront brand page and /shop?brand=slug both show the product
        $this->get("/brand/{$brand->slug}")
            ->assertOk()
            ->assertSee('Botanical Atelier SEO')
            ->assertSee('Atelier Face Oil');

        $this->get("/shop?brand={$brand->slug}")
            ->assertOk()
            ->assertSee('Atelier Face Oil');

        // Homepage renders Brand::active()
        $this->get('/')
            ->assertOk()
            ->assertSee('Botanical Atelier')
            ->assertSee("/brand/{$brand->slug}");
    }

    public function test_inactive_brand_page_returns_404(): void
    {
        $brand = Brand::create([
            'name' => 'Hidden Brand',
            'slug' => 'hidden-brand',
            'is_active' => false,
        ]);

        $this->get("/brand/{$brand->slug}")->assertNotFound();
    }
}

