<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => ['nullable', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'string', 'in:percentage,fixed_amount'],
            'value' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'min_order_amount' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after_or_equal:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'string', 'in:active,inactive,expired'],
            'description' => ['nullable', 'string', 'max:1000'],
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
            'code.unique' => 'The coupon code has already been taken.',
            'type.required' => 'The coupon type field is required.',
            'type.in' => 'The coupon type must be either percentage or fixed_amount.',
            'value.required' => 'The discount value field is required.',
            'value.numeric' => 'The discount value must be a number.',
            'value.min' => 'The discount value must be at least 0.',
            'min_order_amount.required' => 'The minimum order amount field is required.',
            'min_order_amount.numeric' => 'The minimum order amount must be a number.',
            'min_order_amount.min' => 'The minimum order amount must be at least 0.',
            'max_discount_amount.numeric' => 'The maximum discount amount must be a number.',
            'max_discount_amount.min' => 'The maximum discount amount must be at least 0.',
            'usage_limit.integer' => 'The usage limit must be an integer.',
            'usage_limit.min' => 'The usage limit must be at least 1.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or later.',
            'end_date.required' => 'The end date field is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active, inactive, or expired.',
            'description.max' => 'The description may not be greater than 1000 characters.',
        ];
    }
}
