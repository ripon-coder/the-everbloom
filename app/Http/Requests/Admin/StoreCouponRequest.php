<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon');
        $type = (string) $this->input('type');

        $valueRules = ['required', 'numeric', 'min:0'];
        if ($type === 'percentage') {
            $valueRules[] = 'max:100';
        }

        $expiresRules = ['nullable', 'date'];
        if ($this->filled('starts_at')) {
            $expiresRules[] = 'after:starts_at';
        }

        return [
            'code' => [
                'required',
                'string',
                'max:60',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'type' => ['required', 'in:percentage,fixed,shipping'],
            'value' => $valueRules,
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'label' => ['nullable', 'string', 'max:255'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_customer_limit' => ['nullable', 'integer', 'min:1'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => $expiresRules,
            'is_active' => ['nullable', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }
}

