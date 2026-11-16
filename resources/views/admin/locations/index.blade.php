@extends('layouts.admin')

@section('title', 'Bangladesh Locations (District -> Thana -> Area)')

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fw-bold mb-1">Bangladesh Locations</h4>
            <p class="text-muted mb-0 fs-13">Manage Districts, Thanas, and Areas used on Storefront Checkout &amp; Customer Addresses.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.locations.index', ['tab' => 'districts']) }}" class="btn btn-sm {{ $tab === 'districts' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Districts ({{ $districts->count() }})
            </a>
            <a href="{{ route('admin.locations.index', ['tab' => 'thanas', 'district_id' => $selectedDistrictId]) }}" class="btn btn-sm {{ $tab === 'thanas' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Thanas ({{ $thanas->count() }})
            </a>
            <a href="{{ route('admin.locations.index', ['tab' => 'areas', 'district_id' => $selectedDistrictId, 'thana_id' => $selectedThanaId]) }}" class="btn btn-sm {{ $tab === 'areas' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Areas ({{ $areas->count() }})
            </a>
        </div>
    </div>
</div>

@if($tab === 'districts')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add District</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.locations.districts.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">English Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bangla Name</label>
                            <input type="text" name="bn_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Delivery Charge (Optional)</label>
                            <input type="number" step="0.01" name="delivery_charge" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add District</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0">Districts</h5>
                    <form action="{{ route('admin.locations.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="districts">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search district...">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>Name</th>
                                    <th>Bangla Name</th>
                                    <th>Delivery Charge</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($districts as $district)
                                    <tr>
                                        <td>
                                            <input type="text" name="name" value="{{ $district->name }}" form="update-district-{{ $district->id }}" class="form-control form-control-sm" style="min-width:130px;">
                                        </td>
                                        <td>
                                            <input type="text" name="bn_name" value="{{ $district->bn_name }}" form="update-district-{{ $district->id }}" class="form-control form-control-sm" style="min-width:110px;">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="delivery_charge" value="{{ $district->delivery_charge }}" form="update-district-{{ $district->id }}" class="form-control form-control-sm" style="width:90px;">
                                        </td>
                                        <td>
                                            <span class="badge {{ $district->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                {{ $district->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <form id="update-district-{{ $district->id }}" action="{{ route('admin.locations.districts.update', $district) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-soft-primary">Save</button>
                                                </form>
                                                <form action="{{ route('admin.locations.districts.toggle', $district) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $district->is_active ? 'btn-soft-warning' : 'btn-soft-success' }}">
                                                        {{ $district->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.locations.index', ['tab' => 'thanas', 'district_id' => $district->id]) }}" class="btn btn-sm btn-soft-secondary">Thanas</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif($tab === 'thanas')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add Thana / Upazila</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.locations.thanas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">District *</label>
                            <select name="district_id" class="form-select" required>
                                @foreach($allDistricts as $d)
                                    <option value="{{ $d->id }}" {{ (string) $selectedDistrictId === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thana Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bangla Name</label>
                            <input type="text" name="bn_name" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Thana</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0">Thanas</h5>
                    <form action="{{ route('admin.locations.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="thanas">
                        @if($selectedDistrictId)<input type="hidden" name="district_id" value="{{ $selectedDistrictId }}">@endif
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search thana...">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>District</th>
                                    <th>Thana Name</th>
                                    <th>Bangla Name</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($thanas as $thana)
                                    <tr>
                                        <td>{{ $thana->district?->name }}</td>
                                        <td>
                                            <input type="text" name="name" value="{{ $thana->name }}" form="update-thana-{{ $thana->id }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text" name="bn_name" value="{{ $thana->bn_name }}" form="update-thana-{{ $thana->id }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <span class="badge {{ $thana->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                {{ $thana->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <form id="update-thana-{{ $thana->id }}" action="{{ route('admin.locations.thanas.update', $thana) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-soft-primary">Save</button>
                                                </form>
                                                <form action="{{ route('admin.locations.thanas.toggle', $thana) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $thana->is_active ? 'btn-soft-warning' : 'btn-soft-success' }}">
                                                        {{ $thana->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.locations.index', ['tab' => 'areas', 'district_id' => $thana->district_id, 'thana_id' => $thana->id]) }}" class="btn btn-sm btn-soft-secondary">Areas</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add Area / Locality</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.locations.areas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Thana *</label>
                            <select name="thana_id" class="form-select" required>
                                @foreach($thanas as $t)
                                    <option value="{{ $t->id }}" {{ (string) $selectedThanaId === (string) $t->id ? 'selected' : '' }}>{{ $t->district?->name }} — {{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bangla Name</label>
                            <input type="text" name="bn_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Area</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0">Areas</h5>
                    <form action="{{ route('admin.locations.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="areas">
                        @if($selectedDistrictId)<input type="hidden" name="district_id" value="{{ $selectedDistrictId }}">@endif
                        @if($selectedThanaId)<input type="hidden" name="thana_id" value="{{ $selectedThanaId }}">@endif
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search area...">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>District / Thana</th>
                                    <th>Area Name</th>
                                    <th>Postal Code</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areas as $area)
                                    <tr>
                                        <td class="fs-12 text-muted">{{ $area->district?->name }} / {{ $area->thana?->name }}</td>
                                        <td>
                                            <input type="text" name="name" value="{{ $area->name }}" form="update-area-{{ $area->id }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text" name="postal_code" value="{{ $area->postal_code }}" form="update-area-{{ $area->id }}" class="form-control form-control-sm" style="width:90px;">
                                        </td>
                                        <td>
                                            <span class="badge {{ $area->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                {{ $area->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <form id="update-area-{{ $area->id }}" action="{{ route('admin.locations.areas.update', $area) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-soft-primary">Save</button>
                                                </form>
                                                <form action="{{ route('admin.locations.areas.toggle', $area) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $area->is_active ? 'btn-soft-warning' : 'btn-soft-success' }}">
                                                        {{ $area->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
