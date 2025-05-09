@extends('admin.partials.app', ['title' => 'Create Product'])
@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="app-card app-card-settings shadow-sm p-4">
                <div class="app-card-body">
                    <form class="settings-form" action="{{ route('admin.product.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="brand_id" class="form-label">Select Brand</label>
                            <select id="brand_id" name="brand_id" class="form-control">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Select Category</label>
                            <select id="parent_id" name="categorie_id" class="form-control">
                                <option value="">None</option>
                                @include('components.category-select', [
                                    'categories' => $categories,
                                    'prefix' => '',
                                ])
                            </select>
                        </div>



                        <div class="mb-3">
                            <label for="name" class="form-label">Name*</label>
                            <input type="text" class="form-control  @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail*</label>
                            <input type="file" class="form-control  @error('thumbnail') is-invalid @enderror"
                                id="thumbnail" name="thumbnail" accept="image/*">
                            @error('thumbnail')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" multiple class="form-control  @error('images') is-invalid @enderror"
                                id="images" name="images" accept="image/*">
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Product Description</label>
                            <textarea id="summernote" name="description" class="form-control  @error('description') is-invalid @enderror">
                                {{ old('description') }}
                            </textarea>
                            @error('images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="setting-input-3" class="form-label">Status*</label>
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
                        <div class="mb-3">
                            <label for="setting-input-3" class="form-label">Product Feature*</label>
                            <select class="form-select w-auto" name="feature">
                                @foreach (\App\ProductFeatureEnum::cases() as $filter)
                                    <option value="{{ $filter->value }}"
                                        {{ old('feature') === $filter->value ? 'selected' : '' }}>
                                        {{ $filter->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('feature')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @include('components.product-variant')
                        <button type="submit" class="btn app-btn-primary mt-4">Save Changes</button>
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#parent_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select category',
                allowClear: true
            });

            $('#brand_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select brand',
                allowClear: true
            });
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear', 'fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Merriweather',
                    'Roboto', 'Times New Roman', 'Verdana'
                ],
                fontNamesIgnoreCheck: ['Merriweather', 'Roboto'] // Add custom fonts here
            });

        });
        document.addEventListener('DOMContentLoaded', function() {
            const addVariantBtn = document.querySelector('.variant_add_btn a');
            addVariantBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const firstVariant = document.querySelector(".each_div");
                const clone = firstVariant.cloneNode(true);
                clone.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
                clone.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0;
                });
                document.querySelector('.variant_middle_div').appendChild(clone);
            })

            const mainVariantDiv = document.querySelector('.variant_middle_div');
            mainVariantDiv.addEventListener("click", function(e) {
                const removeBtn = e.target.closest('.rm_button_variant a');
                if (removeBtn) {
                    e.preventDefault();
                    const allVariants = mainVariantDiv.querySelectorAll('.each_div');
                    const targetDiv = removeBtn.closest('.each_div');
                    if (targetDiv === allVariants[0]) {
                        alert("You can't delete the first variant.");
                        return;
                    }
                    targetDiv.remove();
                }
            })
        })
    </script>
@endsection
