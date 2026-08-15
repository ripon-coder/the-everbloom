<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', 'unique:products,slug'],
            'short_description' => ['nullable','max:500'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'is_free_delivery' => ['nullable', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'product_type' => ['nullable', 'string', 'in:single,variant'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'simple_stock' => ['nullable', 'integer', 'min:0'],
            'simple_buying_price' => ['nullable', 'numeric', 'min:0'],
            'simple_discount_price' => ['nullable', 'numeric', 'min:0'],
            'simple_sku' => ['nullable', 'string', 'max:255'],
            'simple_weight' => ['nullable', 'numeric', 'min:0'],
            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.buying_price' => ['required_with:variants', 'numeric', 'min:0', 'decimal:0,2'],
            'variants.*.sell_price' => ['required_with:variants', 'numeric', 'min:0', 'decimal:0,2'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.weight' => ['required_with:variants', 'numeric', 'min:0', 'decimal:0,2'],
            'variants.*.status' => ['required_with:variants', 'string', 'in:active,inactive'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.attributes.*.attribute_id' => ['required_with:variants.*.attributes', 'exists:attributes,id'],
            'variants.*.attributes.*.attribute_value_id' => ['required_with:variants.*.attributes', 'exists:attribute_values,id'],
            'variants.*.images' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Get the custom error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.required' => 'The brand field is required.',
            'brand_id.exists' => 'The selected brand is invalid.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'name.required' => 'The product name field is required.',
            'name.unique' => 'The product name has already been taken.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
            'images.*.image' => 'The file must be an image.',
            'images.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'The image may not be greater than 2MB.',
            'variants.*.sku.required_with' => 'The SKU field is required when variants are provided.',
            'variants.*.sku.unique' => 'The SKU has already been taken.',
            'variants.*.buying_price.required_with' => 'The variant buying price field is required when variants are provided.',
            'variants.*.buying_price.numeric' => 'The variant buying price must be a number.',
            'variants.*.buying_price.min' => 'The variant buying price must be at least 0.',
            'variants.*.sell_price.required_with' => 'The variant sell price field is required when variants are provided.',
            'variants.*.sell_price.numeric' => 'The variant sell price must be a number.',
            'variants.*.sell_price.min' => 'The variant sell price must be at least 0.',
            'variants.*.discount_price.numeric' => 'The variant discount price must be a number.',
            'variants.*.discount_price.min' => 'The variant discount price must be at least 0.',
            'variants.*.stock.required_with' => 'The stock field is required when variants are provided.',
            'variants.*.stock.integer' => 'The stock must be an integer.',
            'variants.*.stock.min' => 'The stock must be at least 0.',
            'variants.*.status.required_with' => 'The status field is required when variants are provided.',
            'variants.*.status.in' => 'The variant status must be either active or inactive.',
            'variants.*.attributes.*.attribute_id.required_with' => 'The attribute ID is required when variant attributes are provided.',
            'variants.*.attributes.*.attribute_id.exists' => 'The selected attribute is invalid.',
            'variants.*.attributes.*.attribute_value_id.required_with' => 'The attribute value ID is required when variant attributes are provided.',
            'variants.*.attributes.*.attribute_value_id.exists' => 'The selected attribute value is invalid.',
            'variants.*.images.*.image' => 'The variant image file must be an image.',
            'variants.*.images.*.mimes' => 'The variant image must be a file of type: jpeg, png, jpg, gif, webp.',
            'variants.*.images.*.max' => 'The variant image may not be greater than 2MB.',
        ];
    }
}
