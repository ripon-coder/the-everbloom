<?php

namespace Tests\Feature;

use App\Mail\ContactReplyMail;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\ContactInquiry;
use App\Models\Faq;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase16CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_crud_pages_and_sanitized_content_renders_on_storefront(): void
    {
        $admin = Admin::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.pages.store'), [
                'title' => 'Our Botanical Philosophy',
                'slug' => 'botanical-philosophy',
                'content' => '<p>Pure botanical care.</p><script>alert("xss")</script>',
                'meta_title' => 'Philosophy | The Everbloom',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.cms.pages'));

        $page = Page::where('slug', 'botanical-philosophy')->firstOrFail();

        $this->get(route('cms.page', $page->slug))
            ->assertOk()
            ->assertSee('Our Botanical Philosophy')
            ->assertSee('Pure botanical care.')
            ->assertDontSee('<script>alert("xss")</script>', false);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.cms.pages.update', $page), [
                'title' => 'Updated Philosophy',
                'slug' => 'botanical-philosophy',
                'content' => '<p>Updated content.</p>',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.cms.pages'));

        $this->assertEquals('Updated Philosophy', $page->fresh()->title);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.cms.pages.destroy', $page))
            ->assertRedirect(route('admin.cms.pages'));

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_admin_can_manage_banners_sections_and_faqs(): void
    {
        $admin = Admin::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.banners.store'), [
                'title' => 'Autumn Glow Hero',
                'position' => 'hero',
                'link_url' => '/shop',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.cms.banners'));

        $this->assertDatabaseHas('banners', ['title' => 'Autumn Glow Hero']);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.sections.store'), [
                'key' => 'trust_badges',
                'title' => 'Botanical Purity',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.cms.banners'));

        $this->assertDatabaseHas('homepage_sections', ['key' => 'trust_badges']);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.faqs.store'), [
                'category' => 'Shipping',
                'question' => 'Do you deliver across Bangladesh?',
                'answer' => 'Yes, we deliver to all 64 districts.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.cms.faqs'));

        $faq = Faq::where('question', 'Do you deliver across Bangladesh?')->firstOrFail();
        $this->get(route('faq'))->assertOk();

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.cms.faqs.destroy', $faq))
            ->assertRedirect(route('admin.cms.faqs'));

        $banner = Banner::where('title', 'Autumn Glow Hero')->firstOrFail();
        $this->actingAs($admin, 'admin')
            ->delete(route('admin.cms.banners.destroy', $banner))
            ->assertRedirect(route('admin.cms.banners'));
    }

    public function test_admin_can_manage_inquiries_and_newsletter_subscribers(): void
    {
        Mail::fake();
        $admin = Admin::firstOrFail();

        $inquiry = ContactInquiry::create([
            'name' => 'Farhana',
            'email' => 'farhana@example.com',
            'message' => 'When will the rosehip serum restock?',
            'status' => 'unread',
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.inquiries.read', $inquiry))
            ->assertRedirect();
        $this->assertEquals('read', $inquiry->fresh()->status);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.inquiries.reply', $inquiry), [
                'admin_reply' => 'It will restock this Friday!',
            ])
            ->assertRedirect();

        Mail::assertQueued(ContactReplyMail::class);
        $this->assertEquals('replied', $inquiry->fresh()->status);

        $sub = NewsletterSubscriber::create([
            'email' => 'subscriber@example.com',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.cms.subscribers.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=utf-8');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.cms.subscribers.toggle', $sub))
            ->assertRedirect();
        $this->assertFalse($sub->fresh()->is_active);
    }
}

