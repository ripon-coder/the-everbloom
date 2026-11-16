<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryHierarchyAndFinancialMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_three_step_category_hierarchy_enforces_max_3_levels(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        // Level 1: Main
        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'name' => 'Botanical Skincare',
            'slug' => 'botanical-skincare',
        ])->assertRedirect('/admin/categories');
        $level1 = Category::where('slug', 'botanical-skincare')->firstOrFail();
        $this->assertSame(1, $level1->level);

        // Level 2: Subcategory
        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'parent_id' => $level1->id,
            'name' => 'Facial Treatments',
            'slug' => 'facial-treatments',
        ])->assertRedirect('/admin/categories');
        $level2 = Category::where('slug', 'facial-treatments')->firstOrFail();
        $this->assertSame(2, $level2->level);

        // Level 3: Child Category
        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'parent_id' => $level2->id,
            'name' => 'Night Elixirs',
            'slug' => 'night-elixirs',
        ])->assertRedirect('/admin/categories');
        $level3 = Category::where('slug', 'night-elixirs')->firstOrFail();
        $this->assertSame(3, $level3->level);
        $this->assertSame('Botanical Skincare > Facial Treatments > Night Elixirs', $level3->hierarchy_path);
        $this->assertContains($level3->id, $level1->descendantAndSelfIds());

        // Level 4 must be rejected by validation
        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'parent_id' => $level3->id,
            'name' => 'Invalid Step 4',
            'slug' => 'invalid-step-4',
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_category_scoped_attributes_appear_on_product_create_and_inherit_from_parent(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $spfAttr = Attribute::create([
            'name' => 'SPF Rating',
            'slug' => 'spf-rating',
            'display_type' => 'button',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $sunscreens = Category::where('slug', 'sunscreens')->firstOrFail();
        $sunscreens->assignedAttributes()->sync([$spfAttr->id]);

        $this->assertSame([$spfAttr->id], $sunscreens->effectiveAttributeIds());

        $this->actingAs($admin, 'admin')->get('/admin/products/create')
            ->assertOk()
            ->assertSee('filterAttributesByCategory', false)
            ->assertSee('data-attribute-id="' . $spfAttr->id . '"', false);
    }

    public function test_dedicated_financial_menu_renders_and_filters_by_period_category_vendor_and_status(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $serums = Category::where('slug', 'serums')->firstOrFail();
        $vendor = Vendor::firstOrFail();

        $this->actingAs($admin, 'admin')->get('/admin/financial')
            ->assertOk()
            ->assertSee('Net Booked Revenue')
            ->assertSee('Total Wholesale COGS')
            ->assertSee('Net Realized Profit')
            ->assertSee('Available Inventory Cost Valuation');

        $this->actingAs($admin, 'admin')->get('/admin/financial?' . http_build_query([
            'period' => 'this_year',
            'category_id' => $serums->id,
            'vendor_id' => $vendor->id,
            'status' => 'all_valid',
        ]))->assertOk();
    }
}
