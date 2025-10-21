<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDistrictRequest extends FormRequest
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
        $distinctId = (int) $this->route('district') ?? $this->route('id');
        return [
            'name' => [
                'required',
                'max:200',
                Rule::unique('districts', 'name')->ignore($distinctId),
            ],
            "delivery_charge" => "required|numeric",
            "information" => "nullable|max:1000"
        ];
    }
}
