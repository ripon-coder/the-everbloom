<?php

namespace App\Services;

use App\Repositories\Contracts\AttributeRepository;
use App\Models\Attribute;

class AttributeService
{
    protected $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Get all attributes with pagination.
     */
    public function getAll(int $perPage = 10)
    {
        return $this->attributeRepository->getAll($perPage);
    }

    /**
     * Get all attributes by category ID.
     */
    public function getByCategoryId(int $categoryId, bool $onlyActive = true)
    {
        return $this->attributeRepository->getByCategoryId($categoryId, $onlyActive);
    }

    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute
    {
        return $this->attributeRepository->findById($id);
    }

    /**
     * Find an attribute by slug.
     */
    public function findBySlug(string $slug): ?Attribute
    {
        return $this->attributeRepository->findBySlug($slug);
    }

    /**
     * Create a new attribute.
     */
    public function create(array $data): Attribute
    {
        return $this->attributeRepository->create($data);
    }

    /**
     * Update an existing attribute.
     */
    public function update(int $id, array $data): Attribute
    {
        return $this->attributeRepository->update($id, $data);
    }

    /**
     * Delete an attribute.
     */
    public function delete(int $id): bool
    {
        return $this->attributeRepository->delete($id);
    }

    /**
     * Get attribute types.
     */
    public function getTypes(): array
    {
        return $this->attributeRepository->getTypes();
    }

    /**
     * Update attribute sort order.
     */
    public function updateSortOrder(array $data): bool
    {
        return $this->attributeRepository->updateSortOrder($data);
    }

    /**
     * Prepare options for form display.
     */
    public function prepareOptionsForForm(?array $options): array
    {
        if (empty($options)) {
            return [''];
        }

        return $options;
    }

    /**
     * Get attributes for a specific category formatted for product forms.
     */
    public function getAttributesForProductForm(int $categoryId): array
    {
        $attributes = $this->getByCategoryId($categoryId, true);
        $result = [];

        foreach ($attributes as $attribute) {
            $result[] = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'type' => $attribute->type,
                'required' => $attribute->is_required,
                'options' => $attribute->parsed_options,
                'validation_rules' => $attribute->getValidationRules()
            ];
        }

        return $result;
    }

    /**
     * Validate attribute value based on attribute configuration.
     */
    public function validateAttributeValue(Attribute $attribute, $value): bool
    {
        $rules = $attribute->getValidationRules();
        
        // For simple validation, we'll check basic rules
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            return false;
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            return false;
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            return false;
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            return false;
                        }
                        break;
                    case 'url':
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            return false;
                        }
                        break;
                }
            }
        }

        return true;
    }
}
