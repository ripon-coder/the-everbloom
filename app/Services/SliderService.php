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
        $image = $data['image'] ?? null;
        // Provide a temporary string because the column is NOT NULL
        $data['image'] = 'placeholder.webp'; 
        
        $slider = $this->sliderRepository->create($data);
        
        if ($image) {
            $slider->uploadImage($image, 'sliders');
        }
        
        return $slider;
    }

    public function update($id, array $data)
    {
        $image = $data['image'] ?? null;
        if ($image) {
            unset($data['image']);
        }
        
        $slider = $this->sliderRepository->update($id, $data);
        
        if ($image) {
            $slider->uploadImage($image, 'sliders');
        }
        
        return $slider;
    }
}
