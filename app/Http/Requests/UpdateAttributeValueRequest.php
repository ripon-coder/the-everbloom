<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow admin users to update attribute values
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $attributeValueId = $this->route('attribute_value');

        return [
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0|max:999999.99',
            'status' => 'required|string|in:active,inactive',
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
            'attribute_id.required' => 'The attribute is required.',
            'attribute_id.exists' => 'The selected attribute is invalid.',

            'value.required' => 'The value is required.',
            'value.string' => 'The value must be a string.',
            'value.max' => 'The value may not be greater than 255 characters.',

            'additional_price.required' => 'The additional price is required.',
            'additional_price.numeric' => 'The additional price must be a number.',
            'additional_price.min' => 'The additional price must be at least 0.',
            'additional_price.max' => 'The additional price may not be greater than 99999999.99.',

            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
