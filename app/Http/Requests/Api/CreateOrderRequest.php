<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'product_list' => 'required|array|min:1',
            'product_list.*.product_id' => 'required|integer',
            'product_list.*.variant_id' => 'required|integer',
            'product_list.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|max:20',
            'payment_status' => 'nullable|in:pending,paid,failed',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:100',
            'shipping_address.phone_number' => 'required|string|max:20',
            'shipping_address.district_id' => 'required|integer|exists:districts,id',
            'shipping_address.zone' => 'required|string|max:100',
            'shipping_address.address' => 'required|string|max:255',

        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
