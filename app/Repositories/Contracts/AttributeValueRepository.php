<?php

namespace App\Repositories\Contracts;

use App\Models\AttributeValue;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttributeValueRepository
{
    /**
     * Get all attribute values with pagination.
     */
    public function getAllWithPagination(int $perPage): LengthAwarePaginator;

    /**
     * Get attribute values by product ID.
     */
    public function getByProductId(int $productId);

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


}
