<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.attribute_id' => ['required_with:attributes', 'exists:attributes,id'],
            'attributes.*.attribute_value_id' => ['required_with:attributes', 'exists:attribute_values,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
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
            'product_id.required' => 'The product field is required.',
            'product_id.exists' => 'The selected product is invalid.',
            'sku.required' => 'The SKU field is required.',
            'sku.unique' => 'The SKU has already been taken.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'stock.required' => 'The stock field is required.',
            'stock.integer' => 'The stock must be an integer.',
            'stock.min' => 'The stock must be at least 0.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
            'attributes.*.attribute_id.required_with' => 'The attribute ID is required when attributes are provided.',
            'attributes.*.attribute_id.exists' => 'The selected attribute is invalid.',
            'attributes.*.attribute_value_id.required_with' => 'The attribute value ID is required when attributes are provided.',
            'attributes.*.attribute_value_id.exists' => 'The selected attribute value is invalid.',
            'images.*.image' => 'The file must be an image.',
            'images.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'The image may not be greater than 2MB.',
        ];
    }
}
