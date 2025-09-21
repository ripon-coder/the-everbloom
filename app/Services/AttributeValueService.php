<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Repositories\Contracts\AttributeValueRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributeValueService
{
    protected $attributeValueRepository;

    public function __construct(AttributeValueRepository $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Get all attribute values with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->attributeValueRepository->getAll($perPage);
    }

    /**
     * Get attribute values by product ID.
     */
    public function getByProductId(int $productId): Collection
    {
        return $this->attributeValueRepository->getByProductId($productId);
    }

    /**
     * Get attribute values by attribute ID.
     */
    public function getByAttributeId(int $attributeId): Collection
    {
        return $this->attributeValueRepository->getByAttributeId($attributeId);
    }

    /**
     * Find an attribute value by ID.
     */
    public function findById(int $id): ?AttributeValue
    {
        return $this->attributeValueRepository->findById($id);
    }

    /**
     * Create a new attribute value.
     */
    public function create(array $data): AttributeValue
    {
        // Validate the data
        $this->validateAttributeValue($data);

        return $this->attributeValueRepository->create($data);
    }

    /**
     * Update an existing attribute value.
     */
    public function update(int $id, array $data): AttributeValue
    {
        // Validate the data
        $this->validateAttributeValue($data);

        return $this->attributeValueRepository->update($id, $data);
    }

    /**
     * Delete an attribute value.
     */
    public function delete(int $id): bool
    {
        return $this->attributeValueRepository->delete($id);
    }

    /**
     * Get attribute values for a specific product and attribute.
     */
    public function getByProductAndAttribute(int $productId, int $attributeId): ?AttributeValue
    {
        return $this->attributeValueRepository->getByProductAndAttribute($productId, $attributeId);
    }

    /**
     * Bulk create attribute values for a product.
     */
    public function bulkCreate(int $productId, array $attributeValues): bool
    {
        // Validate each attribute value
        foreach ($attributeValues as $attributeId => $value) {
            $this->validateAttributeValue([
                'product_id' => $productId,
                'attribute_id' => $attributeId,
                'value' => is_array($value) ? null : $value,
                'values' => is_array($value) ? $value : null,
            ]);
        }

        return $this->attributeValueRepository->bulkCreate($productId, $attributeValues);
    }

    /**
     * Update attribute values for a product.
     */
    public function updateProductAttributes(int $productId, array $attributeValues): bool
    {
        // Validate each attribute value
        foreach ($attributeValues as $attributeId => $value) {
            $this->validateAttributeValue([
                'product_id' => $productId,
                'attribute_id' => $attributeId,
                'value' => is_array($value) ? null : $value,
                'values' => is_array($value) ? $value : null,
            ]);
        }

        return $this->attributeValueRepository->updateProductAttributes($productId, $attributeValues);
    }

    /**
     * Get formatted attribute values for product display.
     */
    public function getFormattedProductAttributes(int $productId): array
    {
        $attributeValues = $this->getByProductId($productId);
        $formattedAttributes = [];

        foreach ($attributeValues as $attributeValue) {
            $attribute = $attributeValue->attribute;
            if ($attribute) {
                $formattedAttributes[] = [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'attribute_type' => $attribute->type,
                    'value' => $attributeValue->display_value,
                    'values' => $attributeValue->parsed_values,
                    'is_required' => $attribute->is_required,
                ];
            }
        }

        return $formattedAttributes;
    }

    /**
     * Prepare attribute values for product form.
     */
    public function prepareForProductForm(int $productId): array
    {
        $attributeValues = $this->getByProductId($productId);
        $preparedValues = [];

        foreach ($attributeValues as $attributeValue) {
            if ($attributeValue->values) {
                $preparedValues[$attributeValue->attribute_id] = $attributeValue->values;
            } else {
                $preparedValues[$attributeValue->attribute_id] = $attributeValue->value;
            }
        }

        return $preparedValues;
    }

    /**
     * Validate attribute value data.
     */
    protected function validateAttributeValue(array $data): void
    {
        $attributeValue = new AttributeValue($data);

        if (isset($data['attribute_id'])) {
            $attributeValue->attribute_id = $data['attribute_id'];
        }

        if (!$attributeValue->isValid()) {
            throw new \Exception("Invalid attribute value data");
        }
    }

    /**
     * Get unique values for a specific attribute (useful for filtering).
     */
    public function getUniqueValuesForAttribute(int $attributeId): array
    {
        $attributeValues = $this->getByAttributeId($attributeId);
        $uniqueValues = [];

        foreach ($attributeValues as $attributeValue) {
            $values = $attributeValue->parsed_values;
            foreach ($values as $value) {
                if (!empty($value) && !in_array($value, $uniqueValues)) {
                    $uniqueValues[] = $value;
                }
            }
        }

        sort($uniqueValues);
        return $uniqueValues;
    }

    /**
     * Search attribute values by value content.
     */
    public function searchByValue(string $searchTerm, int $perPage = 10): LengthAwarePaginator
    {
        return AttributeValue::with(['attribute', 'product'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('value', 'like', "%{$searchTerm}%")
                      ->orWhereJsonContains('values', $searchTerm);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get attribute values count by attribute.
     */
    public function getValuesCountByAttribute(): array
    {
        return AttributeValue::join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->select('attributes.name', 'attributes.type', DB::raw('count(attribute_values.id) as total_values'))
            ->groupBy('attributes.id', 'attributes.name', 'attributes.type')
            ->orderBy('total_values', 'desc')
            ->get()
            ->toArray();
    }
}
