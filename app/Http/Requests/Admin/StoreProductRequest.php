<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    protected function productId(): int|string|null
    {
        return null;
    }

    public function rules(): array
    {
        $productId = $this->productId();

        return [
            'name' => ['required', 'string', 'max:180'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'category_slug' => ['required', 'string', 'exists:categories,slug'],
            'slug' => [
                'nullable',
                'string',
                'max:160',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:60',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'compare_at_price' => ['nullable', 'numeric', 'gt:price', 'max:99999999'],
            'stock_count' => ['nullable', 'integer', 'min:0'],
            'min_threshold' => ['nullable', 'integer', 'min:0'],
            'stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'badge' => ['nullable', 'string', 'max:60'],
            'is_featured' => ['nullable', 'boolean'],
            'best_seller' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'how_to_use' => ['nullable', 'string'],
            'skin_types' => ['nullable'],
            'benefits' => ['nullable'],
            'key_ingredients' => ['nullable'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string', 'max:255'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['string', 'max:80'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.title' => ['nullable', 'string', 'max:180'],
            'variants.*.sku' => ['nullable', 'string', 'max:80'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999'],
            'variants.*.stock_count' => ['nullable', 'integer', 'min:0'],
            'variants.*.stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variant_images' => ['nullable', 'array'],
            'variant_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $variants = $this->input('variants', []);
            if (! is_array($variants)) {
                return;
            }

            $seenSkus = [];
            $productId = $this->productId();

            foreach (array_values($variants) as $idx => $variant) {
                $vSku = trim((string) ($variant['sku'] ?? ''));
                if ($vSku === '') {
                    continue;
                }

                $lower = strtolower($vSku);
                if (in_array($lower, $seenSkus, true)) {
                    $validator->errors()->add("variants.{$idx}.sku", "Duplicate variant SKU [{$vSku}] in submission.");
                    continue;
                }
                $seenSkus[] = $lower;

                $existsQuery = ProductVariant::query()->where('sku', $vSku);
                if (! empty($variant['id'])) {
                    $existsQuery->where('id', '!=', (int) $variant['id']);
                } elseif ($productId !== null) {
                    $existsQuery->where('product_id', '!=', (int) $productId);
                }

                if ($existsQuery->exists()) {
                    $validator->errors()->add("variants.{$idx}.sku", "The variant SKU [{$vSku}] has already been taken.");
                }
            }
        });
    }
}

