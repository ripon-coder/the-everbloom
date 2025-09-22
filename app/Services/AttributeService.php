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
    public function getAll()
    {
        return $this->attributeRepository->getAll();
    }

    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute
    {
        return $this->attributeRepository->findById($id);
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
}
