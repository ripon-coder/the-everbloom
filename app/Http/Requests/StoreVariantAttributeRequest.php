<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariantAttributeRequest extends FormRequest
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
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'attribute_id' => ['required', 'exists:attributes,id'],
            'attribute_value_id' => ['required', 'exists:attribute_values,id'],
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
            'product_variant_id.required' => 'The product variant field is required.',
            'product_variant_id.exists' => 'The selected product variant is invalid.',
            'attribute_id.required' => 'The attribute field is required.',
            'attribute_id.exists' => 'The selected attribute is invalid.',
            'attribute_value_id.required' => 'The attribute value field is required.',
            'attribute_value_id.exists' => 'The selected attribute value is invalid.',
        ];
    }
}
