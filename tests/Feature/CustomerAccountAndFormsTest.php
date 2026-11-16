<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Area;
use App\Models\ContactInquiry;
use App\Models\District;
use App\Models\NewsletterSubscriber;
use App\Models\Review;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountAndFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_crud_saved_addresses(): void
    {
        $this->seed();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        $createRes = $this->actingAs($user, 'web')->postJson('/account/addresses', [
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address_line1' => 'House 99, Road 11',
            'postal_code' => '1212',
            'is_default' => false,
        ]);

        $createRes->assertStatus(201)->assertJson(['success' => true]);
        $addressId = $createRes->json('address.id');

        $this->actingAs($user, 'web')->putJson("/account/addresses/{$addressId}", [
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address_line1' => 'House 100, Road 11',
            'postal_code' => '1212',
        ])->assertOk();

        $this->assertDatabaseHas('addresses', [
            'id' => $addressId,
            'address_line1' => 'House 100, Road 11',
            'country' => 'Bangladesh',
            'district_name' => 'Dhaka',
        ]);

        $this->actingAs($user, 'web')->patchJson("/account/addresses/{$addressId}/default")->assertOk();
        $this->assertTrue((bool) Address::find($addressId)->is_default);

        $this->actingAs($user, 'web')->deleteJson("/account/addresses/{$addressId}")->assertOk();
        $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
    }

    public function test_customer_can_update_profile_and_password(): void
    {
        $this->seed();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $this->actingAs($user, 'web')->putJson('/account/profile', [
            'first_name' => 'Eleanor',
            'last_name' => 'Vance-Updated',
            'email' => 'eleanor.vance@example.com',
            'phone' => '+1 (503) 555-9999',
        ])->assertOk();

        $this->assertSame('Vance-Updated', $user->fresh()->last_name);

        $this->actingAs($user, 'web')->putJson('/account/password', [
            'current_password' => 'password',
            'password' => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewPass123', $user->fresh()->password));
    }

    public function test_contact_newsletter_and_review_submissions_persist(): void
    {
        $this->seed();

        $this->postJson('/contact', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'message' => 'Question about serums.',
        ])->assertStatus(201);
        $this->assertSame(1, ContactInquiry::count());

        $this->postJson('/newsletter', [
            'email' => 'subscriber@example.com',
        ])->assertStatus(201);
        $this->assertTrue(NewsletterSubscriber::where('email', 'subscriber@example.com')->exists());

        $this->postJson('/shop/product/botanical-radiance-serum/reviews', [
            'author' => 'Jane Doe',
            'rating' => 5,
            'headline' => 'Lovely serum',
            'body' => 'Feels wonderful on dry skin.',
        ])->assertStatus(201);
        $this->assertTrue(Review::where('author', 'Jane Doe')->where('status', 'Pending')->exists());
    }
}
