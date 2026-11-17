<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\FlashSale\FlashSaleService;
use App\Services\ImageStorageService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminFlashSaleController extends Controller
{
    public function __construct(
        private readonly FlashSaleService $flashSaleService,
        private readonly ImageStorageService $imageStorage,
    ) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $flashSales = FlashSale::query()
            ->withCount('items')
            ->withSum('items as total_sold_qty', 'sold_qty')
            ->when($statusFilter !== '' && $statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'scheduled' THEN 1 WHEN status = 'draft' THEN 2 WHEN status = 'disabled' THEN 3 ELSE 4 END")
            ->orderByDesc('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.flash-sales.index', compact('flashSales', 'statusFilter', 'search'));
    }

    public function create(): View
    {
        return view('admin.flash-sales.create', [
            'flashSale' => new FlashSale([
                'status' => FlashSale::STATUS_SCHEDULED,
                'show_on_homepage' => true,
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCampaign($request);

        $slug = $this->uniqueSlug($validated['slug'] ?? $validated['title']);

        $bannerPath = null;
        $imageSource = 'local';
        if ($request->hasFile('banner_image')) {
            $stored = $this->imageStorage->uploadAsWebp($request->file('banner_image'), 'flash-sales', $slug);
            $bannerPath = $stored['path'];
            $imageSource = $stored['image_source'];
        }

        $flashSale = FlashSale::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'banner_image' => $bannerPath,
            'image_source' => $imageSource,
            'starts_at' => Carbon::parse($validated['starts_at']),
            'ends_at' => Carbon::parse($validated['ends_at']),
            'status' => $this->resolveInitialStatus($validated['status'], Carbon::parse($validated['starts_at']), Carbon::parse($validated['ends_at'])),
            'show_on_homepage' => $request->boolean('show_on_homepage'),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        Cache::forget('storefront_catalog_json');

        return redirect()
            ->route('admin.flash-sales.products', $flashSale)
            ->with('success', 'Flash sale campaign created. Now assign products and sale prices.');
    }

    public function show(FlashSale $flashSale): View
    {
        $flashSale->load(['items.product', 'items.variant', 'creator']);

        $totalUnitsSold = (int) $flashSale->items->sum('sold_qty');
        $totalSaleStock = (int) $flashSale->items->sum('sale_stock');

        $orderStats = OrderItem::query()
            ->where('flash_sale_id', $flashSale->id)
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'Cancelled'))
            ->selectRaw('COALESCE(SUM(line_total), 0) as revenue, COALESCE(SUM(line_profit), 0) as profit, COUNT(DISTINCT order_id) as orders_count')
            ->first();

        $recentOrderItems = OrderItem::with(['order', 'product'])
            ->where('flash_sale_id', $flashSale->id)
            ->latest('id')
            ->limit(15)
            ->get();

        return view('admin.flash-sales.show', compact(
            'flashSale',
            'totalUnitsSold',
            'totalSaleStock',
            'orderStats',
            'recentOrderItems'
        ));
    }

    public function edit(FlashSale $flashSale): View
    {
        return view('admin.flash-sales.create', [
            'flashSale' => $flashSale,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $validated = $this->validateCampaign($request, $flashSale->id);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = Carbon::parse($validated['ends_at']);

        // Check existing items against new window for overlaps (D-08)
        if (! in_array($validated['status'], [FlashSale::STATUS_DRAFT, FlashSale::STATUS_DISABLED], true)) {
            foreach ($flashSale->items as $item) {
                $conflict = $this->flashSaleService->findOverlappingSale(
                    $item->product_id,
                    $item->product_variant_id,
                    $startsAt,
                    $endsAt,
                    $flashSale->id
                );
                if ($conflict) {
                    throw ValidationException::withMessages([
                        'starts_at' => ["Updating dates overlaps with active/scheduled sale \"{$conflict->title}\" for an assigned product."],
                    ]);
                }
            }
        }

        $slug = $this->uniqueSlug($validated['slug'] ?? $validated['title'], $flashSale->id);

        $bannerPath = $flashSale->banner_image;
        $imageSource = $flashSale->image_source ?: 'local';
        if ($request->hasFile('banner_image')) {
            if ($bannerPath && $imageSource !== 'local') {
                $this->imageStorage->delete($bannerPath, $imageSource);
            }
            $stored = $this->imageStorage->uploadAsWebp($request->file('banner_image'), 'flash-sales', $slug);
            $bannerPath = $stored['path'];
            $imageSource = $stored['image_source'];
        }

        $flashSale->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'banner_image' => $bannerPath,
            'image_source' => $imageSource,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $this->resolveInitialStatus($validated['status'], $startsAt, $endsAt),
            'show_on_homepage' => $request->boolean('show_on_homepage'),
        ]);

        Cache::forget('storefront_catalog_json');

        return redirect()
            ->route('admin.flash-sales.index')
            ->with('success', 'Flash sale campaign updated.');
    }

    public function toggleStatus(FlashSale $flashSale): RedirectResponse
    {
        if ($flashSale->status === FlashSale::STATUS_DISABLED || $flashSale->status === FlashSale::STATUS_DRAFT) {
            $now = now();
            if ($flashSale->ends_at->lte($now)) {
                $flashSale->update(['status' => FlashSale::STATUS_EXPIRED]);
            } elseif ($flashSale->starts_at->lte($now)) {
                $flashSale->update(['status' => FlashSale::STATUS_ACTIVE]);
            } else {
                $flashSale->update(['status' => FlashSale::STATUS_SCHEDULED]);
            }
            $message = 'Flash sale activated.';
        } else {
            $flashSale->update(['status' => FlashSale::STATUS_DISABLED]);
            $message = 'Flash sale disabled.';
        }

        Cache::forget('storefront_catalog_json');

        return back()->with('success', $message);
    }

    public function products(FlashSale $flashSale): View
    {
        $flashSale->load(['items.product', 'items.variant']);
        $allProducts = Product::with('productVariants')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.flash-sales.products', compact('flashSale', 'allProducts'));
    }

    public function syncProducts(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0.01'],
            'items.*.sale_stock' => ['required', 'integer', 'min:1'],
            'items.*.max_qty_per_customer' => ['nullable', 'integer', 'min:1'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ]);

        $submitted = $validated['items'] ?? [];
        $seenKeys = [];
        $prepared = [];

        foreach ($submitted as $idx => $row) {
            /** @var Product $product */
            $product = Product::findOrFail((int) $row['product_id']);
            $variantId = ! empty($row['product_variant_id']) ? (int) $row['product_variant_id'] : null;
            $variant = $variantId ? ProductVariant::where('product_id', $product->id)->findOrFail($variantId) : null;

            $key = $product->id . ':' . ($variant?->id ?? '0');
            if (isset($seenKeys[$key])) {
                throw ValidationException::withMessages([
                    "items.{$idx}.product_id" => ["Duplicate product/variant entry for {$product->name}."],
                ]);
            }
            $seenKeys[$key] = true;

            $regularPrice = $variant ? (float) $variant->price : (float) $product->price;
            $availableStock = $variant ? (int) $variant->stock_count : (int) $product->stock_count;
            $salePrice = round((float) $row['sale_price'], 2);
            $saleStock = (int) $row['sale_stock'];

            if ($salePrice >= $regularPrice) {
                throw ValidationException::withMessages([
                    "items.{$idx}.sale_price" => ["Sale price for {$product->name} must be lower than regular price ({$regularPrice})."],
                ]);
            }

            if ($saleStock > $availableStock) {
                throw ValidationException::withMessages([
                    "items.{$idx}.sale_stock" => ["Flash sale stock ({$saleStock}) for {$product->name} cannot exceed available stock ({$availableStock})."],
                ]);
            }

            // Per D-08: disallow overlapping flash sales on same product/variant
            if (! in_array($flashSale->status, [FlashSale::STATUS_DRAFT, FlashSale::STATUS_DISABLED], true)) {
                $conflict = $this->flashSaleService->findOverlappingSale(
                    $product->id,
                    $variant?->id,
                    $flashSale->starts_at,
                    $flashSale->ends_at,
                    $flashSale->id
                );
                if ($conflict) {
                    throw ValidationException::withMessages([
                        "items.{$idx}.product_id" => ["{$product->name} already belongs to overlapping flash sale \"{$conflict->title}\"."],
                    ]);
                }
            }

            $existing = FlashSaleProduct::where('flash_sale_id', $flashSale->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variant?->id)
                ->first();

            $soldQty = $existing ? (int) $existing->sold_qty : 0;
            if ($saleStock < $soldQty) {
                throw ValidationException::withMessages([
                    "items.{$idx}.sale_stock" => ["Sale stock for {$product->name} cannot be less than units already sold ({$soldQty})."],
                ]);
            }

            $prepared[] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'sale_price' => $salePrice,
                'sale_stock' => $saleStock,
                'sold_qty' => $soldQty,
                'max_qty_per_customer' => ! empty($row['max_qty_per_customer']) ? (int) $row['max_qty_per_customer'] : null,
                'is_active' => array_key_exists('is_active', $row) ? (bool) $row['is_active'] : true,
            ];
        }

        // Remove items no longer in the list (unless they have orders)
        $existingItems = $flashSale->items()->get();
        foreach ($existingItems as $existingItem) {
            $k = $existingItem->product_id . ':' . ($existingItem->product_variant_id ?? '0');
            if (! isset($seenKeys[$k])) {
                $existingItem->delete();
            }
        }

        foreach ($prepared as $itemData) {
            FlashSaleProduct::updateOrCreate(
                [
                    'flash_sale_id' => $flashSale->id,
                    'product_id' => $itemData['product_id'],
                    'product_variant_id' => $itemData['product_variant_id'],
                ],
                $itemData
            );
        }

        Cache::forget('storefront_catalog_json');

        return redirect()
            ->route('admin.flash-sales.products', $flashSale)
            ->with('success', 'Flash sale products updated.');
    }

    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        if (OrderItem::where('flash_sale_id', $flashSale->id)->exists()) {
            return back()->withErrors([
                'flash_sale' => 'Cannot delete a flash sale referenced by existing orders. Disable it instead.',
            ]);
        }

        if ($flashSale->banner_image && ($flashSale->image_source ?: 'local') !== 'local') {
            $this->imageStorage->delete($flashSale->banner_image, $flashSale->image_source);
        }

        $flashSale->delete();
        Cache::forget('storefront_catalog_json');

        return redirect()
            ->route('admin.flash-sales.index')
            ->with('success', 'Flash sale deleted.');
    }

    private function validateCampaign(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('flash_sales', 'slug')->ignore($ignoreId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in([
                FlashSale::STATUS_DRAFT,
                FlashSale::STATUS_SCHEDULED,
                FlashSale::STATUS_ACTIVE,
                FlashSale::STATUS_DISABLED,
            ])],
            'show_on_homepage' => ['nullable', 'boolean'],
        ]);
    }

    private function resolveInitialStatus(string $requestedStatus, Carbon $startsAt, Carbon $endsAt): string
    {
        if (in_array($requestedStatus, [FlashSale::STATUS_DRAFT, FlashSale::STATUS_DISABLED], true)) {
            return $requestedStatus;
        }

        $now = now();
        if ($endsAt->lte($now)) {
            return FlashSale::STATUS_EXPIRED;
        }
        if ($startsAt->lte($now)) {
            return FlashSale::STATUS_ACTIVE;
        }

        return FlashSale::STATUS_SCHEDULED;
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'flash-sale';
        $original = $slug;
        $counter = 1;

        while (FlashSale::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . (++$counter);
        }

        return $slug;
    }
}
