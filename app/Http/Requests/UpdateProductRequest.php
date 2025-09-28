<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')->id;
        
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name,' . $productId],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['required_with:variants', 'string', 'max:255', function ($attribute, $value, $fail) use ($productId) {
                // Check if SKU already exists for this product but exclude current variant being edited
                $skuCheck = \App\Models\ProductVariant::where('sku', $value)
                    ->where('product_id', $productId)
                    ->exists();
                
                // If SKU exists for this product, we need to check if it's the current variant being updated
                if ($skuCheck) {
                    // Extract the variant index from the attribute name (e.g., "variants.0.sku" -> "0")
                    $variantIndex = explode('.', $attribute)[1];
                    $variantId = $this->input("variants.{$variantIndex}.id");
                    
                    // Check if there's a variant with this SKU and ID (meaning it's the same variant being updated)
                    $existingVariant = \App\Models\ProductVariant::where('sku', $value)
                        ->where('product_id', $productId)
                        ->where('id', $variantId)
                        ->first();
                    
                    // If no existing variant found with this ID, then it's a duplicate SKU
                    if (!$existingVariant) {
                        $fail('The SKU has already been taken for this product.');
                    }
                }
                
                // Check if SKU exists for other products
                $otherProductSku = \App\Models\ProductVariant::where('sku', $value)
                    ->where('product_id', '!=', $productId)
                    ->exists();
                    
                if ($otherProductSku) {
                    $fail('The SKU has already been taken by another product.');
                }
            }],
            'variants.*.price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.status' => ['required_with:variants', 'string', 'in:active,inactive'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.attributes.*.attribute_id' => ['required_with:variants.*.attributes', 'exists:attributes,id'],
            'variants.*.attributes.*.attribute_value_id' => ['required_with:variants.*.attributes', 'exists:attribute_values,id'],
            'variants.*.images' => ['nullable', 'array'],
            'variants.*.images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
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
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
            'images.*.image' => 'The file must be an image.',
            'images.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'The image may not be greater than 2MB.',
            'variants.*.sku.required_with' => 'The SKU field is required when variants are provided.',
            'variants.*.sku.unique' => 'The SKU has already been taken.',
            'variants.*.price.numeric' => 'The variant price must be a number.',
            'variants.*.price.min' => 'The variant price must be at least 0.',
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
