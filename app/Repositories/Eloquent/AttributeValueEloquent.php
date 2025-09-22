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
        return $this->model->with(['attribute'])
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: $perPage);
    }

    /**
     * Get attribute values by product ID.
     */
    public function getByProductId(int $productId)
    {
        return $this->model->with('attribute')->get();
    }



    /**
     * Find an attribute value by ID.
     */
    public function findById(int $id): ?AttributeValue
    {
        return $this->model->find($id);
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

}
