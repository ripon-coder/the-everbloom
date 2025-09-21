<?php

namespace App\Repositories\Contracts;

use App\Models\Attribute;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttributeRepository
{
    /**
     * Get all attributes with pagination.
     */
    public function getAll(): LengthAwarePaginator;

    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute;


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

}
