<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true to allow authorized users to update brands
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brandId = $this->route('brand') ?? $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'logo' =>"nullable|image|max:2048",
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
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
            'name.required' => 'The brand name is required.',
            'name.string' => 'The brand name must be a string.',
            'name.max' => 'The brand name may not be greater than 255 characters.',
            'name.unique' => 'The brand name has already been taken.',

            'slug.required' => 'The slug is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.max' => 'The slug may not be greater than 255 characters.',
            'slug.unique' => 'The slug has already been taken.',
            'slug.regex' => 'The slug may only contain letters, numbers, and hyphens.',

            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 1000 characters.',

            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either active or inactive.',

            'options.array' => 'The options must be an array.',
            'options.*.string' => 'Each option must be a string.',
        ];
    }
}
