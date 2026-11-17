<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\Banner;
use App\Models\ContactInquiry;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Services\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCmsController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage
    ) {}

    // ─── PAGES ────────────────────────────────────────────────────────────────

    public function pages(): View
    {
        $pages = Page::orderBy('title')->paginate(15);

        return view('admin.cms.pages', compact('pages'));
    }

    public function storePage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['slug'] ?: $validated['title']) ?: 'page';

        Page::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.cms.pages')->with('success', 'Page created.');
    }

    public function editPage(Page $page): View
    {
        return view('admin.cms.page-edit', compact('page'));
    }

    public function updatePage(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('pages', 'slug')->ignore($page->id)],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $page->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug'] ?: $validated['title']) ?: $page->slug,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.cms.pages')->with('success', 'Page updated.');
    }

    public function destroyPage(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.cms.pages')->with('success', 'Page deleted.');
    }

    // ─── BANNERS & HOMEPAGE SECTIONS ──────────────────────────────────────────

    public function banners(): View
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->get();
        $sections = HomepageSection::orderBy('sort_order')->get();

        return view('admin.cms.banners', compact('banners', 'sections'));
    }

    public function storeBanner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'position' => ['required', 'in:hero,promo,trust_badge'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        $imageSource = 'local';
        if ($request->hasFile('image')) {
            $stored = $this->imageStorage->uploadAsWebp($request->file('image'), 'banners', Str::slug($validated['title']));
            $imagePath = $stored['path'];
            $imageSource = $stored['image_source'];
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imagePath,
            'image_source' => $imageSource,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'position' => $validated['position'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.cms.banners')->with('success', 'Banner created.');
    }

    public function updateBanner(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'position' => ['required', 'in:hero,promo,trust_badge'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $banner->image;
        $imageSource = $banner->image_source ?: 'local';
        if ($request->hasFile('image')) {
            if ($imagePath && $imageSource !== 'local') {
                $this->imageStorage->delete($imagePath, $imageSource);
            }
            $stored = $this->imageStorage->uploadAsWebp($request->file('image'), 'banners', Str::slug($validated['title']));
            $imagePath = $stored['path'];
            $imageSource = $stored['image_source'];
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $imagePath,
            'image_source' => $imageSource,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'position' => $validated['position'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.cms.banners')->with('success', 'Banner updated.');
    }

    public function destroyBanner(Banner $banner): RedirectResponse
    {
        if ($banner->image && ($banner->image_source ?: 'local') !== 'local') {
            $this->imageStorage->delete($banner->image, $banner->image_source);
        }
        $banner->delete();

        return redirect()->route('admin.cms.banners')->with('success', 'Banner deleted.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:80', 'unique:homepage_sections,key'],
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        HomepageSection::create([
            'key' => Str::slug($validated['key'], '_'),
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.cms.banners')->with('success', 'Homepage section saved.');
    }

    public function updateSection(Request $request, HomepageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $section->update([
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.cms.banners')->with('success', 'Homepage section updated.');
    }

    // ─── FAQS ─────────────────────────────────────────────────────────────────

    public function faqs(): View
    {
        $faqs = Faq::orderBy('category')->orderBy('sort_order')->paginate(20);

        return view('admin.cms.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Faq::create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?: 'General',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.cms.faqs')->with('success', 'FAQ added.');
    }

    public function updateFaq(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?: 'General',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.cms.faqs')->with('success', 'FAQ updated.');
    }

    public function destroyFaq(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.cms.faqs')->with('success', 'FAQ deleted.');
    }

    // ─── CONTACT INQUIRIES INBOX ──────────────────────────────────────────────

    public function inquiries(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();

        $inquiries = ContactInquiry::query()
            ->when($statusFilter !== '' && $statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.cms.inquiries', compact('inquiries', 'statusFilter'));
    }

    public function markInquiryRead(ContactInquiry $inquiry): RedirectResponse
    {
        $inquiry->update(['status' => 'read']);

        return back()->with('success', 'Inquiry marked as read.');
    }

    public function replyInquiry(Request $request, ContactInquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to($inquiry->email)->queue(new ContactReplyMail($inquiry, $validated['admin_reply']));

        $inquiry->update([
            'status' => 'replied',
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
            'replied_by' => Auth::guard('admin')->id(),
        ]);

        return back()->with('success', 'Reply queued and inquiry marked as replied.');
    }

    public function destroyInquiry(ContactInquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return back()->with('success', 'Inquiry deleted.');
    }

    // ─── NEWSLETTER SUBSCRIBERS ───────────────────────────────────────────────

    public function subscribers(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $subscribers = NewsletterSubscriber::query()
            ->when($search !== '', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.cms.subscribers', compact('subscribers', 'search'));
    }

    public function toggleSubscriber(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->update(['is_active' => ! $subscriber->is_active]);

        return back()->with('success', 'Subscriber status updated.');
    }

    public function exportSubscribers(): StreamedResponse
    {
        $rows = NewsletterSubscriber::where('is_active', true)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Email', 'Active', 'Subscribed At']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->email,
                    $row->is_active ? 'Yes' : 'No',
                    $row->created_at?->toDateTimeString() ?? '',
                ]);
            }
            fclose($out);
        }, 'newsletter-subscribers.csv', ['Content-Type' => 'text/csv']);
    }
}

