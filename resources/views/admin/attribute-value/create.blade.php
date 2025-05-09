@extends('admin.partials.app', ['title' => 'Create Attribute Value'])
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form" action="{{ route('admin.attribute-value.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="attribute_id" class="form-label">Select Attribute</label>
                            <select id="attribute_id" name="attribute_id" class="form-control">
                                @foreach ($attributes as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="value" class="form-label">Value*</label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror" id="value"
                                name="value" value="{{ old('value') }}">
                            @error('value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status*</label>
                            <select class="form-select w-auto" name="status">
                                @foreach (\App\AttributeEnum::cases() as $filter)
                                    <option value="{{ $filter->value }}"
                                        {{ old('status') === $filter->value ? 'selected' : '' }}>
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
@section('stylesheet')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#attribute_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select an attribute',
                allowClear: true
            });
        });
    </script>
@endsection