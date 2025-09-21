<?php

namespace App\Repositories\Contracts;

use App\Models\Attribute;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttributeRepository
{
    /**
     * Get all attributes with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get all attributes by category ID.
     */
    public function getByCategoryId(int $categoryId, bool $onlyActive = true);

    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute;

    /**
     * Find an attribute by slug.
     */
    public function findBySlug(string $slug): ?Attribute;

    /**
     * Create a new attribute.
     */
    public function create(array $data): Attribute;

    /**
     * Update an existing attribute.
     */
    public function update(int $id, array $data): Attribute;

    /**
     * Delete an attribute.
     */
    public function delete(int $id): bool;

    /**
     * Get attribute types.
     */
    public function getTypes(): array;

    /**
     * Update attribute sort order.
     */
    public function updateSortOrder(array $data): bool;
}
