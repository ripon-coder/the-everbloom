<?php

namespace App\Repositories\Contracts;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttributeRepository
{
    public function getAllWithPagination(array $filters = []): LengthAwarePaginator;

    public function getAll();

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
