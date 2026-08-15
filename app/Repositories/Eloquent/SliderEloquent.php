<?php
namespace App\Repositories\Eloquent;

use App\Models\Slider;
use App\Repositories\Contracts\SliderRepository;

class SliderEloquent implements SliderRepository
{
    public function all(array $filters = [])
    {
        $query = Slider::query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('subtitle', 'LIKE', "%{$search}%")
                  ->orWhere('btn_text', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('sort_order', 'asc')->paginate(15)->withQueryString();
    }

    public function findById($id)
    {
        return Slider::findOrFail($id);
    }

    public function create(array $data)
    {
        return Slider::create($data);
    }

    public function update($id, array $data)
    {
        $slider = $this->findById($id);
        $slider->update($data);
        return $slider;
    }

    public function delete($id)
    {
        $slider = $this->findById($id);
        return $slider->delete();
    }
}
