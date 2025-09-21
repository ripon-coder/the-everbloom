<?php

namespace App\Repositories\Contracts;

use App\Models\AttributeValue;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttributeValueRepository
{
    /**
     * Get all attribute values with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get attribute values by product ID.
     */
    public function getByProductId(int $productId);

    /**
     * Get attribute values by attribute ID.
     */
    public function getByAttributeId(int $attributeId);

    /**
     * Find an attribute value by ID.
     */
    public function findById(int $id): ?AttributeValue;

    /**
     * Create a new attribute value.
     */
    public function create(array $data): AttributeValue;

    /**
     * Update an existing attribute value.
     */
    public function update(int $id, array $data): AttributeValue;

    /**
     * Delete an attribute value.
     */
    public function delete(int $id): bool;

    /**
     * Get attribute values for a specific product and attribute.
     */
    public function getByProductAndAttribute(int $productId, int $attributeId);

    /**
     * Bulk create attribute values for a product.
     */
    public function bulkCreate(int $productId, array $attributeValues): bool;

    /**
     * Update attribute values for a product.
     */
    public function updateProductAttributes(int $productId, array $attributeValues): bool;
}
