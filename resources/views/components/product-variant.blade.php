<div class=" main_variant_div">
    <div class="variant_middle_div">
        <div class="each_div border p-4 mt-1">
            <div class="rm_button_variant">
                <a class="btn-sm app-btn-danger p-1" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-trash-fill" viewBox="0 0 16 16">
                        <path
                            d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                    </svg>
                </a>
            </div>
            <div class="mb-3">
                <label for="sku" class="form-label">SKU*</label>
                <input type="text" class="form-control  @error('sku') is-invalid @enderror" id="sku"
                    name="sku" value="{{ old('sku') }}">
                @error('sku')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Discount Price*</label>
                <input type="text" class="form-control  @error('discount') is-invalid @enderror" id="discount"
                    name="sku" value="{{ old('discount') }}">
                @error('discount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price*</label>
                <input type="text" class="form-control  @error('discount') is-invalid @enderror" id="price"
                    name="sku" value="{{ old('discount') }}">
                @error('price')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock*</label>
                <input type="text" class="form-control  @error('stock') is-invalid @enderror" id="stock"
                    name="sku" value="{{ old('stock') }}">
                @error('stock')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="setting-input-3" class="form-label">Variant Status*</label>
                <select class="form-select w-auto" name="v_status">
                    @foreach (\App\VariantEnum::cases() as $filter)
                        <option value="{{ $filter->value }}" {{ old('v_status') === $filter->value ? 'selected' : '' }}>
                            {{ $filter->label() }}
                        </option>
                    @endforeach
                </select>
                @error('v_status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="attribute_main p-4">
                <div class="attribute_for_variant border p-3">
                    <div class="attribute_option">
                        <label for="price" class="form-label">Attribute</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="attribute_value">
                        <label for="price" class="form-label">Attribute Value</label>
                        <input type="text" class="form-control">
                    </div>
                    <div><a class="btn app-btn-danger" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                                width="1em" height="1em" fill="currentColor" class="bi bi-trash-fill"
                                viewBox="0 0 16 16">
                                <path
                                    d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                            </svg></a></div>
                    <div ><a class="btn app-btn-primary" href="#"> <svg xmlns="http://www.w3.org/2000/svg"
                                width="1em" height="1em" fill="currentColor" class="bi bi-plus-circle me-1"
                                viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z" />
                                <path
                                    d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z" />
                            </svg></a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="variant_add_btn">
        <a class="btn app-btn-secondary" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                class="bi bi-plus-circle me-1" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z" />
                <path
                    d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z" />
            </svg>
            Add Variant
        </a>
    </div>
</div>
