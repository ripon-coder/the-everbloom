<?php

namespace App\Repositories\Eloquent;

use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRepository as AttributeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get all attributes by category ID.
     */
    public function getByCategoryId(int $categoryId, bool $onlyActive = true)
    {
        $query = $this->model->where('category_id', $categoryId);

        if ($onlyActive) {
            $query->active();
        }

        return $query->ordered()->get();
    }

    /**
     * Find an attribute by ID.
     */
    public function findById(int $id): ?Attribute
    {
        return $this->model->with('category')->find($id);
    }

    /**
     * Find an attribute by slug.
     */
    public function findBySlug(string $slug): ?Attribute
    {
        return $this->model->with('category')->where('slug', $slug)->first();
    }

    /**
     * Create a new attribute.
     */
    public function create(array $data): Attribute
    {
        // Handle options array
        if (isset($data['options']) && is_array($data['options'])) {
            // Filter out empty values and reindex
            $data['options'] = array_values(array_filter($data['options'], function($value) {
                return !empty(trim($value));
            }));
            
            // If no valid options, set to null
            if (empty($data['options'])) {
                $data['options'] = null;
            }
        }

        // Set default values for boolean fields
        $data['is_required'] = $data['is_required'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $this->model->create($data);
    }

    /**
     * Update an existing attribute.
     */
    public function update(int $id, array $data): Attribute
    {
        $attribute = $this->findById($id);

        if (!$attribute) {
            throw new \Exception("Attribute not found");
        }

        // Handle options array
        if (isset($data['options']) && is_array($data['options'])) {
            // Filter out empty values and reindex
            $data['options'] = array_values(array_filter($data['options'], function($value) {
                return !empty(trim($value));
            }));
            
            // If no valid options, set to null
            if (empty($data['options'])) {
                $data['options'] = null;
            }
        }

        // Handle boolean fields
        if (isset($data['is_required'])) {
            $data['is_required'] = (bool) $data['is_required'];
        }
        if (isset($data['is_active'])) {
            $data['is_active'] = (bool) $data['is_active'];
        }

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

    /**
     * Get attribute types.
     */
    public function getTypes(): array
    {
        return Attribute::getTypes();
    }

    /**
     * Update attribute sort order.
     */
    public function updateSortOrder(array $data): bool
    {
        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                $this->model->where('id', $item['id'])->update([
                    'sort_order' => $item['sort_order']
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
