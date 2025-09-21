<?php

namespace App\Repositories\Eloquent;

use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRepository as AttributeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeEloquent implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(Attribute $attribute)
    {
        $this->model = $attribute;
    }

    /**
     * Get all attributes with pagination.
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->model->orderBy('name')->paginate(15);
    }


    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute
    {
        return $this->model->find($id);
    }


    /**
     * Create a new attribute.
     */
    public function create(array $data): Attribute
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing attribute.
     */
    public function update(int $id, array $data): Attribute
    {
        $attribute = $this->findById($id);
        $attribute->update($data);
        return $attribute->fresh();
    }

    /**
     * Delete an attribute.
     */
    public function delete(int $id): bool
    {
        $attribute = $this->findById($id);

        if (!$attribute) {
            return false;
        }

        return $attribute->delete();
    }

}
