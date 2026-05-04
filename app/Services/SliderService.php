<?php
namespace App\Services;

use App\Repositories\Contracts\SliderRepository;

class SliderService
{
    protected $sliderRepository;

    public function __construct(SliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }

    public function create(array $data)
    {
        $slider = $this->sliderRepository->create($data);
        if (isset($data['image']) && $data['image']) {
            $slider->uploadImage($data['image'], 'slider_image');
        }
        return $slider;
    }

    public function update($id, array $data)
    {
        $slider = $this->sliderRepository->update($id, $data);
        if (isset($data['image']) && $data['image']) {
            $slider->uploadImage($data['image'], 'slider_image');
        }
        return $slider;
    }
}
