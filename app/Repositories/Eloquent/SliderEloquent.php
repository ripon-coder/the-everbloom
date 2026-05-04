<?php
namespace App\Repositories\Eloquent;

use App\Models\Slider;
use App\Repositories\Contracts\SliderRepository;

class SliderEloquent implements SliderRepository
{
    public function all()
    {
        return Slider::orderBy('sort_order', 'asc')->get();
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
