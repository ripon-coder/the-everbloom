<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlashSaleRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:flash_sales,slug', 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date', 'after_or_equal:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'string', 'in:active,inactive,expired'],
            'banner_image' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['exists:products,id'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
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
            'name.required' => 'The flash sale name field is required.',
            'name.max' => 'The flash sale name may not be greater than 255 characters.',
            'slug.unique' => 'The flash sale slug has already been taken.',
            'slug.alpha_dash' => 'The flash sale slug may only contain letters, numbers, dashes, and underscores.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or later.',
            'end_date.required' => 'The end date field is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active, inactive, or expired.',
            'banner_image.max' => 'The banner image path may not be greater than 255 characters.',
            'products.required' => 'At least one product must be selected for the flash sale.',
            'products.array' => 'The products must be an array.',
            'products.min' => 'At least one product must be selected for the flash sale.',
            'products.*.exists' => 'One or more selected products do not exist.',
            'discount_price.numeric' => 'The discount price must be a number.',
            'discount_price.min' => 'The discount price must be greater than or equal to 0.',
            'discount_percentage.numeric' => 'The discount percentage must be a number.',
            'discount_percentage.min' => 'The discount percentage must be greater than or equal to 0.',
            'discount_percentage.max' => 'The discount percentage must not be greater than 100.',
        ];
    }
}
