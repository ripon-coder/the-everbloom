<?php

namespace App\Repositories\Eloquent;

use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRepository as AttributeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttributeEloquent implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(Attribute $attribute)
    {
        $this->model = $attribute;
    }

    public function getAllWithPagination(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->withCount('attributeValues');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->paginate(15)->withQueryString();
    }
    public function getAll()
    {
        return $this->model->active()->get(["id", "name"]);
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
