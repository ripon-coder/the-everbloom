<?php

namespace App\Http\Requests;

use App\Constants\CategoryStatus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true to allow authorized users to update categories
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:' . CategoryStatus::ACTIVE . ',' . CategoryStatus::INACTIVE,
            'is_featured' => 'nullable|boolean',
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
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'name.unique' => 'The category name has already been taken.',

            'slug.required' => 'The slug is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.max' => 'The slug may not be greater than 255 characters.',
            'slug.unique' => 'The slug has already been taken.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',

            'parent_id.exists' => 'The selected parent category is invalid.',

            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 1000 characters.',

            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either active or inactive.',

            'options.array' => 'The options must be an array.',
            'options.*.string' => 'Each option must be a string.',
        ];
    }
}
