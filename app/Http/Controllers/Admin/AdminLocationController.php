<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLocationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'districts');
        $selectedDistrictId = $request->query('district_id');
        $selectedThanaId = $request->query('thana_id');

        $allDistricts = District::orderBy('sort_order')->orderBy('name')->get();

        $districts = District::withCount(['thanas', 'areas'])
            ->when($request->filled('search') && $tab === 'districts', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $thanas = Thana::with('district')
            ->withCount('areas')
            ->when($selectedDistrictId, fn ($q) => $q->where('district_id', $selectedDistrictId))
            ->when($request->filled('search') && $tab === 'thanas', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('district_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $areas = Area::with(['district', 'thana'])
            ->when($selectedDistrictId, fn ($q) => $q->where('district_id', $selectedDistrictId))
            ->when($selectedThanaId, fn ($q) => $q->where('thana_id', $selectedThanaId))
            ->when($request->filled('search') && $tab === 'areas', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('district_id')
            ->orderBy('thana_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.locations.index', compact(
            'tab',
            'allDistricts',
            'districts',
            'thanas',
            'areas',
            'selectedDistrictId',
            'selectedThanaId'
        ));
    }

    public function storeDistrict(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'bn_name' => ['nullable', 'string', 'max:120'],
            'delivery_charge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $slug = Str::slug($validated['name']);
        if (District::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        District::create([
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
            'slug' => $slug,
            'delivery_charge' => $validated['delivery_charge'] ?? null,
            'sort_order' => (District::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.locations.index', ['tab' => 'districts'])
            ->with('success', 'District added successfully.');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'bn_name' => ['nullable', 'string', 'max:120'],
            'delivery_charge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $district->update([
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
            'delivery_charge' => $validated['delivery_charge'] ?? null,
        ]);

        return redirect()->route('admin.locations.index', ['tab' => 'districts'])
            ->with('success', 'District renamed successfully.');
    }

    public function toggleDistrict(District $district)
    {
        $district->update(['is_active' => !$district->is_active]);

        return redirect()->back()->with('success', 'District status updated.');
    }

    public function storeThana(Request $request)
    {
        $validated = $request->validate([
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:120'],
            'bn_name' => ['nullable', 'string', 'max:120'],
        ]);

        $district = District::findOrFail($validated['district_id']);

        Thana::create([
            'district_id' => $district->id,
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
            'slug' => Str::slug($district->name . '-' . $validated['name']),
            'sort_order' => (Thana::where('district_id', $district->id)->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.locations.index', ['tab' => 'thanas', 'district_id' => $district->id])
            ->with('success', 'Thana added successfully.');
    }

    public function updateThana(Request $request, Thana $thana)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'bn_name' => ['nullable', 'string', 'max:120'],
        ]);

        $thana->update([
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Thana renamed successfully.');
    }

    public function toggleThana(Thana $thana)
    {
        $thana->update(['is_active' => !$thana->is_active]);

        return redirect()->back()->with('success', 'Thana status updated.');
    }

    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'thana_id' => ['required', 'integer', 'exists:thanas,id'],
            'name' => ['required', 'string', 'max:150'],
            'bn_name' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $thana = Thana::findOrFail($validated['thana_id']);

        Area::create([
            'district_id' => $thana->district_id,
            'thana_id' => $thana->id,
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'sort_order' => (Area::where('thana_id', $thana->id)->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.locations.index', [
            'tab' => 'areas',
            'district_id' => $thana->district_id,
            'thana_id' => $thana->id,
        ])->with('success', 'Area added successfully.');
    }

    public function updateArea(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'bn_name' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $area->update([
            'name' => $validated['name'],
            'bn_name' => $validated['bn_name'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Area renamed successfully.');
    }

    public function toggleArea(Area $area)
    {
        $area->update(['is_active' => !$area->is_active]);

        return redirect()->back()->with('success', 'Area status updated.');
    }
}

