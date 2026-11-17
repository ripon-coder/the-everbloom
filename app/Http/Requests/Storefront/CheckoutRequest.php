<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:40'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address' => ['required', 'string', 'max:500'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'saveAddress' => ['nullable', 'boolean'],
            'shippingMethod' => ['required', 'in:standard,express'],
            'paymentMethod' => ['required', 'in:cod_demo,bkash_demo,nagad_demo,card_demo'],
            'discountCode' => ['nullable', 'string', 'max:60'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.productId' => ['required', 'integer', 'exists:products,id'],
            'items.*.variantId' => ['nullable', 'string', 'max:80'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}

