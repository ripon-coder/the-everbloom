<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase9WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_server_wishlist_endpoints(): void
    {
        $this->getJson('/account/wishlist')->assertUnauthorized();
        $this->postJson('/account/wishlist', ['product_id' => 1])->assertUnauthorized();
        $this->deleteJson('/account/wishlist/1')->assertUnauthorized();
    }

    public function test_authenticated_customer_can_add_merge_list_and_remove_wishlist_items(): void
    {
        $this->seed();

        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $other = User::where('email', 'sophia.laurent@example.com')->firstOrFail();
        $products = Product::where('is_active', true)->take(2)->get();
        $p1 = $products[0];
        $p2 = $products[1];

        // Add single item
        $this->actingAs($user, 'web')
            ->postJson('/account/wishlist', ['product_id' => $p1->id])
            ->assertCreated()
            ->assertJson(['success' => true, 'items' => [$p1->id]]);

        // Merge localStorage batch (duplicate p1 + new p2)
        $this->actingAs($user, 'web')
            ->postJson('/account/wishlist', ['product_ids' => [$p1->id, $p2->id]])
            ->assertCreated();

        $this->assertSame(2, Wishlist::where('user_id', $user->id)->count());

        // Scoped to owner
        $this->actingAs($other, 'web')
            ->getJson('/account/wishlist')
            ->assertOk()
            ->assertJson(['items' => []]);

        // List returns items and storefront product payloads
        $this->actingAs($user, 'web')
            ->getJson('/account/wishlist')
            ->assertOk()
            ->assertJsonCount(2, 'items')
            ->assertJsonCount(2, 'products');

        // Delete one item
        $this->actingAs($user, 'web')
            ->deleteJson("/account/wishlist/{$p1->id}")
            ->assertOk()
            ->assertJson(['success' => true, 'items' => [$p2->id]]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $p1->id,
        ]);
    }
}

