<?php

namespace App\Repositories\Eloquent;

use App\Models\AttributeValue;
use App\Repositories\Contracts\AttributeValueRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AttributeValueEloquent implements AttributeValueRepository
{
    protected $model;

    public function __construct(AttributeValue $model)
    {
        $this->model = $model;
    }

    /**
     * Get all attribute values with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['attribute', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get attribute values by product ID.
     */
    public function getByProductId(int $productId)
    {
        return $this->model->with('attribute')
            ->forProduct($productId)
            ->get();
    }

    /**
     * Get attribute values by attribute ID.
     */
    public function getByAttributeId(int $attributeId)
    {
        return $this->model->with('product')
            ->forAttribute($attributeId)
            ->get();
    }

    /**
     * Find an attribute value by ID.
     */
    public function findById(int $id): ?AttributeValue
    {
        return $this->model->with(['attribute', 'product'])->find($id);
    }

    /**
     * Create a new attribute value.
     */
    public function create(array $data): AttributeValue
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing attribute value.
     */
    public function update(int $id, array $data): AttributeValue
    {
        $attributeValue = $this->findById($id);

        if (!$attributeValue) {
            throw new \Exception("Attribute value not found");
        }

        $attributeValue->update($data);

        return $attributeValue->fresh();
    }

    /**
     * Delete an attribute value.
     */
    public function delete(int $id): bool
    {
        $attributeValue = $this->findById($id);

        if (!$attributeValue) {
            return false;
        }

        return $attributeValue->delete();
    }

    /**
     * Get attribute values for a specific product and attribute.
     */
    public function getByProductAndAttribute(int $productId, int $attributeId)
    {
        return $this->model->forProduct($productId)
            ->forAttribute($attributeId)
            ->first();
    }

    /**
     * Bulk create attribute values for a product.
     */
    public function bulkCreate(int $productId, array $attributeValues): bool
    {
        try {
            DB::beginTransaction();

            // Delete existing attribute values for the product
            $this->model->forProduct($productId)->delete();

            // Create new attribute values
            foreach ($attributeValues as $attributeId => $value) {
                if (!empty($value)) {
                    $data = [
                        'product_id' => $productId,
                        'attribute_id' => $attributeId,
                    ];

                    if (is_array($value)) {
                        $data['values'] = $value;
                        $data['value'] = null;
                    } else {
                        $data['value'] = $value;
                        $data['values'] = null;
                    }

                    $this->model->create($data);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Update attribute values for a product.
     */
    public function updateProductAttributes(int $productId, array $attributeValues): bool
    {
        try {
            DB::beginTransaction();

            // Get existing attribute values for the product
            $existingValues = $this->getByProductId($productId);
            $existingAttributeIds = $existingValues->pluck('attribute_id')->toArray();

            // Update or create attribute values
            foreach ($attributeValues as $attributeId => $value) {
                if (!empty($value)) {
                    $data = [
                        'product_id' => $productId,
                        'attribute_id' => $attributeId,
                    ];

                    if (is_array($value)) {
                        $data['values'] = $value;
                        $data['value'] = null;
                    } else {
                        $data['value'] = $value;
                        $data['values'] = null;
                    }

                    $attributeValue = $this->getByProductAndAttribute($productId, $attributeId);

                    if ($attributeValue) {
                        $attributeValue->update($data);
                    } else {
                        $this->model->create($data);
                    }
                }
            }

            // Remove attribute values that are no longer in the input
            $inputAttributeIds = array_keys($attributeValues);
            $attributeIdsToRemove = array_diff($existingAttributeIds, $inputAttributeIds);

            foreach ($attributeIdsToRemove as $attributeId) {
                $attributeValue = $this->getByProductAndAttribute($productId, $attributeId);
                if ($attributeValue) {
                    $attributeValue->delete();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
