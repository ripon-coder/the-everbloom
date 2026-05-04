<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Contracts\SliderRepository;
use App\Services\SliderService;

class SliderController extends Controller
{
    protected $sliderRepository;
    protected $sliderService;

    public function __construct(SliderRepository $sliderRepository, SliderService $sliderService)
    {
        $this->sliderRepository = $sliderRepository;
        $this->sliderService = $sliderService;
    }

    public function index()
    {
        $sliders = $this->sliderRepository->all();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'btn_text' => 'nullable|string|max:50',
            'btn_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $this->sliderService->create($validated);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully!');
    }

    public function edit(string $id)
    {
        $slider = $this->sliderRepository->findById($id);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'btn_text' => 'nullable|string|max:50',
            'btn_link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $this->sliderService->update($id, $validated);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully!');
    }

    public function destroy(string $id)
    {
        $this->sliderRepository->delete($id);
        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully!');
    }

    public function toggleStatus(string $id)
    {
        $slider = $this->sliderRepository->findById($id);
        $slider->status = !$slider->status;
        $slider->save();

        return response()->json(['success' => true, 'status' => $slider->status]);
    }
}
