@extends('admin.layouts.app')

@section('title', 'Create Attribute Value')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Attribute Value</h2>
        <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
            Back to Attribute Values
        </a>
    </div>
    <form action="{{ route('admin.attribute-values.store') }}" method="POST" class="p-6">
        @csrf
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="attribute_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attribute</label>
                <select name="attribute_id" id="attribute_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('attribute_id') border-red-500 @enderror" required>
                    <option value="">Select an attribute</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}" {{ old('attribute_id') == $attribute->id ? 'selected' : '' }}>
                            {{ $attribute->name }} ({{ $attribute->type_name }})
                        </option>
                    @endforeach
                </select>
                @error('attribute_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="product_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product</label>
                <select name="product_id" id="product_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('product_id') border-red-500 @enderror" required>
                    <option value="">Select a product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Value Input Section -->
        <div class="mb-6">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Value</label>
            
            <!-- Single Value Input -->
            <div id="single-value-container" class="mb-4">
                <input type="text" name="value" id="value" value="{{ old('value') }}" placeholder="Enter attribute value" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('value') border-red-500 @enderror">
                @error('value')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>

            <!-- Multiple Values Input (Hidden by default) -->
            <div id="multiple-values-container" class="hidden">
                <label class="block mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Select multiple values:</label>
                <div id="checkboxes-container" class="space-y-2">
                    <!-- Checkboxes will be dynamically added here based on attribute options -->
                </div>
                <input type="hidden" name="values" id="values" value="">
            </div>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the value for the selected attribute. For checkbox/multi-select attributes, multiple options will be shown.</p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Cancel
            </a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Create Attribute Value
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attributeSelect = document.getElementById('attribute_id');
    const singleValueContainer = document.getElementById('single-value-container');
    const multipleValuesContainer = document.getElementById('multiple-values-container');
    const checkboxesContainer = document.getElementById('checkboxes-container');
    const valuesInput = document.getElementById('values');

    // Store attribute options data
    const attributeOptions = {};

    // Fetch attribute options
    function fetchAttributeOptions(attributeId) {
        if (attributeOptions[attributeId]) {
            updateValueInterface(attributeId);
            return;
        }

        fetch(`/admin/attributes/${attributeId}`)
            .then(response => response.json())
            .then(data => {
                attributeOptions[attributeId] = data;
                updateValueInterface(attributeId);
            })
            .catch(error => {
                console.error('Error fetching attribute options:', error);
            });
    }

    // Update value input interface based on attribute type
    function updateValueInterface(attributeId) {
        const attribute = attributeOptions[attributeId];
        if (!attribute) return;

        const attributeType = attribute.type;
        const options = attribute.options || [];

        // Hide both containers first
        singleValueContainer.classList.add('hidden');
        multipleValuesContainer.classList.add('hidden');

        if (attributeType === 'checkbox' || attributeType === 'select-multiple') {
            // Show multiple values interface
            multipleValuesContainer.classList.remove('hidden');
            
            // Build checkboxes
            checkboxesContainer.innerHTML = '';
            options.forEach(option => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <input type="checkbox" id="option_${option.value}" name="option_values[]" value="${option.value}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="option_${option.value}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">${option.label}</label>
                `;
                checkboxesContainer.appendChild(div);
            });

            // Add change listener to checkboxes
            checkboxesContainer.addEventListener('change', updateValuesInput);
        } else {
            // Show single value interface
            singleValueContainer.classList.remove('hidden');
            
            // Update placeholder based on attribute type
            const valueInput = document.getElementById('value');
            switch (attributeType) {
                case 'text':
                    valueInput.placeholder = 'Enter text value';
                    valueInput.type = 'text';
                    break;
                case 'textarea':
                    valueInput.placeholder = 'Enter long text value';
                    valueInput.type = 'text';
                    break;
                case 'number':
                    valueInput.placeholder = 'Enter number';
                    valueInput.type = 'number';
                    break;
                case 'date':
                    valueInput.placeholder = 'Select date';
                    valueInput.type = 'date';
                    break;
                case 'color':
                    valueInput.placeholder = 'Select color';
                    valueInput.type = 'color';
                    break;
                case 'file':
                    valueInput.placeholder = 'File upload will be handled separately';
                    valueInput.type = 'text';
                    valueInput.disabled = true;
                    break;
                default:
                    valueInput.placeholder = 'Enter value';
                    valueInput.type = 'text';
            }
        }
    }

    // Update hidden values input when checkboxes change
    function updateValuesInput() {
        const checkboxes = checkboxesContainer.querySelectorAll('input[type="checkbox"]:checked');
        const values = Array.from(checkboxes).map(cb => cb.value);
        valuesInput.value = JSON.stringify(values);
    }

    // Listen for attribute selection change
    attributeSelect.addEventListener('change', function() {
        const selectedAttributeId = this.value;
        if (selectedAttributeId) {
            fetchAttributeOptions(selectedAttributeId);
        } else {
            // Hide both containers if no attribute is selected
            singleValueContainer.classList.add('hidden');
            multipleValuesContainer.classList.add('hidden');
        }
    });

    // Initialize with selected attribute if any
    const selectedAttributeId = attributeSelect.value;
    if (selectedAttributeId) {
        fetchAttributeOptions(selectedAttributeId);
    }
});
</script>
@endpush
