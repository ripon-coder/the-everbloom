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

    public function getAllWithPagination(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['attribute']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('value', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('attribute', function($attr) use ($search) {
                      $attr->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['attribute_id'])) {
            $query->where('attribute_id', $filters['attribute_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy("attribute_id")->paginate($perPage)->withQueryString();
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
