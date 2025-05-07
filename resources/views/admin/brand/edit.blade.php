@extends('admin.partials.app', ['title' => 'Edit Brand'])
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form" action="{{ route('admin.brand.update', $brand) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Name*</label>
                            <input type="text" class="form-control  @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ $brand->name }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail</label>
                            <input type="file" class="form-control  @error('thumbnail') is-invalid @enderror"
                                id="thumbnail" name="thumbnail" accept="image/*">
                            @error('thumbnail')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @isset($brand->thumbnail)
                                <img class="mt-2" width="70"
                                    src="{{ asset('dynamic-assets/brand/' . $brand->thumbnail) }}">
                            @endisset
                        </div>
                        <div class="mb-3">
                            <label for="setting-input-3" class="form-label">Status*</label>
                            <select class="form-select w-auto" name="status">
                                @foreach (\App\BrandEnum::cases() as $filter)
                                    <option value="{{ $filter->value }}" @selected($brand->status->value == $filter->value)>
                                        {{ $filter->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                        </div>
                        <button type="submit" class="btn app-btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
