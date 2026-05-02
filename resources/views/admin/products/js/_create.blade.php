<!-- JS -->
    <script>
        // Global variable for variant counting
        let variantCount = 0;
        let nextVariantNumber = 1; // Track the next sequential variant number

        // Helper function to show error messages instead of alerts
        function showValidationError(message, element) {
            // Create error div
            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-3 flex items-center';
            errorDiv.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                ${message}
            `;

            // Insert the error message after the element
            if (element && element.parentNode) {
                element.parentNode.insertBefore(errorDiv, element.nextSibling);
            } else {
                // Fallback to adding at the top of the form
                const form = document.getElementById('productForm');
                if (form) {
                    form.insertBefore(errorDiv, form.firstChild);
                }
            }

            // Remove the error message after 5 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {

            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const productImages = document.getElementById('productImages');
            const imagePreview = document.getElementById('imagePreview');
            const addVariantBtn = document.getElementById('addVariantBtn');
            const variantsContainer = document.getElementById('variantsContainer');

            // Populate variants from old input and display errors
            populateVariantsFromOldInput();

            // Auto-generate slug
            nameInput?.addEventListener('input', () => {
                slugInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g,
                    '-').replace(/-+/g, '-');
            });

            // Image preview
            productImages?.addEventListener('change', function(e) {
                imagePreview.innerHTML = '';
                const files = e.target.files;
                
                if (files && files.length > 0) {
                    Array.from(files).forEach((file, index) => {
                        // Validate file type
                        if (!file.type.startsWith('image/')) {
                            console.error('Invalid file type:', file.type);
                            return;
                        }
                        
                        // Validate file size (2MB max)
                        if (file.size > 2 * 1024 * 1024) {
                            console.error('File too large:', file.size);
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const div = document.createElement('div');
                            div.className = 'image-preview relative group';
                            div.innerHTML = `
                                <img src="${ev.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg border border-gray-300">
                                <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200" data-index="${index}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="text-xs text-gray-500 mt-1 truncate">${file.name}</div>
                            `;
                            
                            const removeBtn = div.querySelector('.remove-image');
                            removeBtn.addEventListener('click', function() {
                                div.remove();
                                // Remove the file from the input
                                const dt = new DataTransfer();
                                const inputFiles = Array.from(productImages.files);
                                inputFiles.splice(index, 1);
                                inputFiles.forEach(file => dt.items.add(file));
                                productImages.files = dt.files;
                            });
                            
                            imagePreview.appendChild(div);
                        };
                        reader.onerror = function() {
                            console.error('Error reading file:', file.name);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });

            // Remove the problematic addOptionBtn code as it's not needed for variant functionality

            // Add variant button (manual)
            addVariantBtn?.addEventListener('click', function() {
                addManualVariantToForm();
            });

            // Form submission validation
            document.getElementById('productForm')?.addEventListener('submit', function(e) {
                const variantsContainer = document.getElementById('variantsContainer');
                if (!variantsContainer) {
                    return; // No variants container found
                }

                const variantElements = variantsContainer.querySelectorAll('[data-variant]');
                let hasValidationErrors = false;

                // Validate all variants for duplicate attributes
                variantElements.forEach(variantElement => {
                    if (variantElement) {
                        const isValid = validateVariantAttributes(variantElement);
                        if (!isValid) {
                            hasValidationErrors = true;
                        }
                    }
                });

                // Prevent form submission if there are validation errors
                if (hasValidationErrors) {
                    e.preventDefault();

                    // Scroll to the first error
                    const firstError = document.querySelector('.duplicate-error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }

                    // Show general error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className =
                        'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center';
                    errorDiv.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Please fix the duplicate attribute errors before submitting the form.
            `;

                    // Insert at the top of the form
                    const form = document.getElementById('productForm');
                    form.insertBefore(errorDiv, form.firstChild);

                    // Remove the error message after 5 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            });

            // Event delegation for add/remove attribute buttons
            document.addEventListener('click', function(e) {
                // Add attribute button
                if (e.target.closest('.add-attribute')) {
                    const button = e.target.closest('.add-attribute');
                    const variantId = button.dataset.variant;
                    addAttributeToVariant(variantId);
                }

                // Remove attribute button
                if (e.target.closest('.remove-attribute')) {
                    const button = e.target.closest('.remove-attribute');
                    const attributeItem = button.closest('.attribute-item');
                    const attributesContainer = button.closest('.attributes-container');

                    // Don't remove if it's the last attribute
                    if (attributesContainer.querySelectorAll('.attribute-item').length > 1) {
                        attributeItem.remove();
                    }
                }
            });

            // Event delegation for attribute selection changes
            document.addEventListener('change', function(e) {
                // Attribute select change
                if (e.target.classList.contains('attribute-select')) {
                    const attributeSelect = e.target;
                    const attributeItem = attributeSelect.closest('.attribute-item');
                    const valueSelect = attributeItem.querySelector('.attribute-value-select');
                    const attrId = attributeSelect.value;

                    valueSelect.innerHTML = '';
                    valueSelect.disabled = true;

                    if (attrId) {
                        // Show loading state
                        valueSelect.innerHTML = '<option value="">Loading...</option>';

                        // Fetch attribute values via AJAX
                        fetch(`/admin/attributes/${attrId}/values`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(values => {
                                valueSelect.innerHTML = '';
                                valueSelect.disabled = false;

                                if (values.length > 0) {
                                    // Add default option
                                    const defaultOpt = document.createElement('option');
                                    defaultOpt.value = "";
                                    defaultOpt.text = "Select value";
                                    defaultOpt.selected = true; // show as default
                                    defaultOpt.disabled = true; // make unselectable after selection
                                    valueSelect.appendChild(defaultOpt);
                                    values.forEach(v => {
                                        const opt = document.createElement('option');
                                        opt.value = v.id;
                                        opt.text = v.value;
                                        valueSelect.appendChild(opt);
                                    });
                                } else {
                                    valueSelect.innerHTML = '<option value="">No values found</option>';
                                }
                            })
                            .catch(error => {
                                console.error('Error loading attribute values:', error);
                                valueSelect.innerHTML =
                                '<option value="">Error loading values</option>';
                            });
                    } else {
                        valueSelect.innerHTML = '<option value="">Select Value</option>';
                    }
                }

                // Attribute value select change - validate for duplicates
                if (e.target.classList.contains('attribute-value-select')) {
                    const valueSelect = e.target;
                    const attributeItem = valueSelect.closest('.attribute-item');
                    const attributeSelect = attributeItem.querySelector('.attribute-select');
                    const variantContainer = attributeItem.closest('[data-variant]');

                    // Only validate if both attribute and value are selected
                    if (attributeSelect.value && valueSelect.value) {
                        // Get all attribute items in this variant
                        const allAttributeItems = variantContainer.querySelectorAll('.attribute-item');

                        // Check for duplicate attribute values across different attributes
                        let hasDuplicateValue = false;

                        // Collect all selected attribute values in this variant (excluding current one)
                        const usedValues = [];
                        allAttributeItems.forEach(item => {
                            // Skip the current attribute item
                            if (item === attributeItem) {
                                return;
                            }

                            const itemValueSelect = item.querySelector('.attribute-value-select');
                            if (itemValueSelect && itemValueSelect.value) {
                                usedValues.push(itemValueSelect.value);
                            }
                        });

                        // Check if the selected value is already used
                        if (usedValues.includes(valueSelect.value)) {
                            hasDuplicateValue = true;
                        }

                        // If duplicate value found, show error and reset selection
                        if (hasDuplicateValue) {
                            showValidationError(
                                'This attribute value is already used in another attribute within this variant. Please select a different value.',
                                attributeItem
                            );
                            valueSelect.value = '';
                            return;
                        }

                        // Also check for duplicate attribute-value combinations
                        let hasDuplicateCombination = false;
                        allAttributeItems.forEach(item => {
                            // Skip the current attribute item
                            if (item === attributeItem) return;

                            const itemAttributeSelect = item.querySelector('.attribute-select');
                            const itemValueSelect = item.querySelector('.attribute-value-select');

                            if (itemAttributeSelect.value === attributeSelect.value &&
                                itemValueSelect.value === valueSelect.value) {
                                hasDuplicateCombination = true;
                            }
                        });

                        // If duplicate combination found, show error and reset selection
                        if (hasDuplicateCombination) {
                            showValidationError(
                                'This attribute and value combination already exists in this variant.',
                                attributeItem
                            );
                            valueSelect.value = '';
                            return;
                        }
                    }
                }
            });

            // Direct event listener attachment for dynamically created elements
            function attachEventListeners() {
                // Remove existing event listeners to avoid duplicates
                document.querySelectorAll('.attribute-value-select').forEach(select => {
                    select.removeEventListener('change', handleAttributeValueChange);
                });

                // Add event listeners to all current attribute value selects
                document.querySelectorAll('.attribute-value-select').forEach(select => {
                    select.addEventListener('change', handleAttributeValueChange);
                });
            }

            // Handler function for attribute value changes
            function handleAttributeValueChange(e) {
                const valueSelect = e.target;
                const attributeItem = valueSelect.closest('.attribute-item');
                const attributeSelect = attributeItem.querySelector('.attribute-select');
                const variantContainer = attributeItem.closest('[data-variant]');

                // Only validate if both attribute and value are selected
                if (attributeSelect.value && valueSelect.value) {
                    // Get all attribute items in this variant
                    const allAttributeItems = variantContainer.querySelectorAll('.attribute-item');

                    // Check for duplicate attribute values across different attributes
                    let hasDuplicateValue = false;

                    // Collect all selected attribute values in this variant (excluding current one)
                    const usedValues = [];
                    allAttributeItems.forEach(item => {
                        // Skip the current attribute item
                        if (item === attributeItem) {
                            return;
                        }

                        const itemValueSelect = item.querySelector('.attribute-value-select');
                        if (itemValueSelect && itemValueSelect.value) {
                            usedValues.push(itemValueSelect.value);
                        }
                    });

                    // Check if the selected value is already used
                    if (usedValues.includes(valueSelect.value)) {
                        hasDuplicateValue = true;
                    }

                    // If duplicate value found, show error and reset selection
                    if (hasDuplicateValue) {
                        showValidationError(
                            'This attribute value is already used in another attribute within this variant. Please select a different value.',
                            attributeItem
                        );
                        valueSelect.value = '';
                        return;
                    }

                    // Also check for duplicate attribute-value combinations
                    let hasDuplicateCombination = false;
                    allAttributeItems.forEach(item => {
                        // Skip the current attribute item
                        if (item === attributeItem) return;

                        const itemAttributeSelect = item.querySelector('.attribute-select');
                        const itemValueSelect = item.querySelector('.attribute-value-select');

                        if (itemAttributeSelect.value === attributeSelect.value &&
                            itemValueSelect.value === valueSelect.value) {
                            hasDuplicateCombination = true;
                        }
                    });

                    // If duplicate combination found, show error and reset selection
                    if (hasDuplicateCombination) {
                        showValidationError(
                            'This attribute and value combination already exists in this variant.',
                            attributeItem
                        );
                        valueSelect.value = '';
                        return;
                    }
                }
            }

            // Fix the addAttributeToVariant function to attach event listeners
            const originalAddAttributeToVariant = addAttributeToVariant;
            addAttributeToVariant = function(variantId) {
                originalAddAttributeToVariant(variantId);

                // Wait a bit for the DOM to update, then attach event listeners
                setTimeout(() => {
                    attachEventListeners();
                }, 50);
            };

            // Fix the addManualVariantToForm function to attach event listeners
            const originalAddManualVariantToForm = addManualVariantToForm;
            addManualVariantToForm = function() {
                originalAddManualVariantToForm();

                // Wait a bit for the DOM to update, then attach event listeners
                setTimeout(() => {
                    attachEventListeners();
                }, 50);
            };

            // Initial attachment of event listeners
            setTimeout(() => {
                attachEventListeners();
            }, 100);

            // Add a more aggressive validation that runs on every change
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('attribute-value-select')) {
                    // Force immediate validation
                    setTimeout(() => {
                        validateAllVariantAttributes();
                    }, 10);
                }
            });

            // Function to validate all variant attributes
            function validateAllVariantAttributes() {
                document.querySelectorAll('[data-variant]').forEach(variantContainer => {
                    const attributeItems = variantContainer.querySelectorAll('.attribute-item');
                    const usedValues = [];

                    attributeItems.forEach(item => {
                        const valueSelect = item.querySelector('.attribute-value-select');
                        const attributeSelect = item.querySelector('.attribute-select');

                        if (valueSelect && attributeSelect && valueSelect.value && attributeSelect
                            .value) {
                            // Check if this value is already used
                            if (usedValues.includes(valueSelect.value)) {
                                // Found duplicate - reset and show error
                                showValidationError(
                                    'This attribute value is already used in another attribute within this variant. Please select a different value.',
                                    item
                                );
                                valueSelect.value = '';
                                return;
                            }

                            // Add to used values
                            usedValues.push(valueSelect.value);
                        }
                    });
                });
            }

        });


        // Preview variant image (single file only)
        function previewVariantImage(input, variantIndex) {
            const previewContainer = document.getElementById(`variant-image-preview-${variantIndex}`);
            previewContainer.innerHTML = '';
            const files = input.files;

            if (files && files.length > 0) {
                // Only process the first file (single image)
                const file = files[0];
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    console.error('Invalid file type:', file.type);
                    input.value = ''; // Clear the input
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    console.error('File too large:', file.size);
                    input.value = ''; // Clear the input
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview relative group inline-block m-1';
                    div.innerHTML = `
                        <div class="relative inline-block">
                            <img src="${e.target.result}" alt="Variant Preview" class="w-32 h-32 object-cover rounded-lg shadow-md border-2 border-gray-200">
                            <button type="button" class="remove-variant-image absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg" title="Remove image">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 truncate max-w-32 text-center">${file.name}</div>
                    `;
                    
                    const removeBtn = div.querySelector('.remove-variant-image');
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        // Clear the file input
                        input.value = '';
                    });
                    
                    previewContainer.appendChild(div);
                };
                reader.onerror = function() {
                    console.error('Error reading file:', file.name);
                    input.value = ''; // Clear the input
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove variant image preview
        function removeVariantImage(button) {
            const previewContainer = button.parentElement.parentElement;
            const inputId = previewContainer.id.replace('variant-image-preview-', 'variant-image-');
            const input = document.getElementById(inputId);

            // Clear the file input
            input.value = '';

            // Remove the preview
            button.parentElement.remove();
        }


        // Add manual variant to form
        function addManualVariantToForm() {
            variantCount++;
            const displayNumber = nextVariantNumber++;

            // Hide empty state and show variants container
            document.getElementById('emptyVariantsState').classList.add('hidden');
            document.getElementById('variantsContainer').classList.remove('hidden');

            const variantHtml = `
    <div class="bg-gradient-to-br from-white dark:from-gray-800 to-gray-50 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden" data-variant="${variantCount}">
        <!-- Variant Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="bg-blue-500 text-white rounded-lg w-8 h-8 flex items-center justify-center font-bold text-sm mr-3">
                        ${displayNumber}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Variant</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Configure your variant options</p>
                    </div>
                </div>
                <button type="button" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg transition duration-200 group" onclick="removeVariant(this)" title="Remove variant">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Variant Body -->
        <div class="p-6">
            <!-- Variant Attributes Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Attributes</h4>
                    </div>
                    <button type="button" class="bg-purple-100 hover:bg-purple-200 text-purple-700 font-medium py-2 px-4 rounded-lg text-sm transition duration-200 flex items-center add-attribute" data-variant="${variantCount}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Attribute
                    </button>
                </div>
                <div class="attributes-container space-y-4" data-variant="${variantCount}">
                    <!-- Initial empty attribute -->
                    <div class="attribute-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600" data-attribute-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Attribute
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-select" name="variants[${variantCount}][attributes][0][attribute_id]" required>
                                    <option value="">Select an attribute</option>
                                    @foreach ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Value
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-value-select" name="variants[${variantCount}][attributes][0][attribute_value_id]" required>
                                    <option value="">Select a value</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-3 px-4 rounded-lg transition duration-200 remove-attribute flex items-center justify-center" title="Remove attribute">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Variant Details Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Details</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            SKU
                        </label>
                        <input type="text" name="variants[${variantCount}][sku]" placeholder="e.g., TSHIRT-BLUE-LARGE" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock
                        </label>
                        <input type="number" name="variants[${variantCount}][stock]" value="10" placeholder="0" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                            Weight (kg) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="variants[${variantCount}][weight]" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    </div>
                </div>
                
                <!-- Variant Pricing Section -->
                <div class="mt-6 py-2 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center mb-4 px-1">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Pricing</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Buying Price ({{  $currency_sign }})
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">{{  $currency_sign }}</span>
                                <input type="number" name="variants[${variantCount}][buying_price]" step="0.01" min="0" value="0" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-buying-price">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Sell Price ({{  $currency_sign }})
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">{{  $currency_sign }}</span>
                                <input type="number" name="variants[${variantCount}][sell_price]" step="0.01" min="0" value="0" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-sell-price" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                Discount Price ({{  $currency_sign }})
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">{{  $currency_sign }}</span>
                                <input type="number" name="variants[${variantCount}][discount_price]" step="0.01" min="0" value="0" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-discount-price">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Status
                        </label>
                        <select name="variants[${variantCount}][status]" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Variant Image Section -->
            <div>
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Image</h4>
                </div>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                    <input type="file" name="variants[${variantCount}][images]" accept="image/*" class="hidden" id="variant-image-${variantCount}" onchange="previewVariantImage(this, ${variantCount})">
                    <label for="variant-image-${variantCount}" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-gray-600 dark:text-gray-300 mb-1">Click to upload variant image</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB (single image only)</p>
                    </label>
                </div>
                <div id="variant-image-preview-${variantCount}" class="mt-4 flex justify-center"></div>
            </div>
        </div>
    </div>`;

            variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
            
            // Attach pricing validation event listeners to the new variant
            setTimeout(() => {
                attachPricingValidation(variantCount);
            }, 100);
        }
        
        // Attach pricing validation to variant inputs
        function attachPricingValidation(variantId) {
            const variantContainer = document.querySelector(`[data-variant="${variantId}"]`);
            if (!variantContainer) return;
            
            const buyingPriceInput = variantContainer.querySelector('.variant-buying-price');
            const sellPriceInput = variantContainer.querySelector('.variant-sell-price');
            const discountPriceInput = variantContainer.querySelector('.variant-discount-price');
            
            function validateVariantPricing() {
                const buyingPrice = parseFloat(buyingPriceInput.value) || 0;
                const sellPrice = parseFloat(sellPriceInput.value) || 0;
                const discountPrice = parseFloat(discountPriceInput.value) || 0;

                // Clear previous validation styles
                [buyingPriceInput, sellPriceInput, discountPriceInput].forEach(input => {
                    input.classList.remove('border-red-500', 'border-green-500');
                });

                // Validate sell price is not less than buying price
                if (sellPrice > 0 && sellPrice < buyingPrice) {
                    sellPriceInput.classList.add('border-red-500');
                } else if (sellPrice >= buyingPrice && sellPrice > 0) {
                    sellPriceInput.classList.add('border-green-500');
                }

                // Validate discount price is not greater than sell price
                if (discountPrice > 0 && discountPrice > sellPrice) {
                    discountPriceInput.classList.add('border-red-500');
                } else if (discountPrice <= sellPrice && discountPrice > 0) {
                    discountPriceInput.classList.add('border-green-500');
                }
            }
            
            // Add event listeners for real-time validation
            [buyingPriceInput, sellPriceInput, discountPriceInput].forEach(input => {
                if (input) {
                    input.addEventListener('input', validateVariantPricing);
                }
            });
        }

        // Remove variant function
        function removeVariant(button) {
            const variant = button.closest('[data-variant]');
            variant.remove();

            // Check if there are any variants left
            const variantsContainer = document.getElementById('variantsContainer');
            if (variantsContainer.children.length === 0) {
                // Show empty state and hide variants container
                document.getElementById('emptyVariantsState').classList.remove('hidden');
                document.getElementById('variantsContainer').classList.add('hidden');
            }
        }

        // Validate variant attributes for duplicates
        function validateVariantAttributes(variantContainer) {
            // Safety check - if variantContainer is null, return true (no validation errors)
            if (!variantContainer) {
                console.error('validateVariantAttributes called with null variantContainer');
                return true;
            }

            // Find all attribute items directly in the variant container
            const attributeItems = variantContainer.querySelectorAll('.attribute-item');
            console.log('validateVariantAttributes: Found attribute items:', attributeItems.length);

            if (attributeItems.length === 0) {
                console.warn('validateVariantAttributes: No attribute items found in variant');
                return true; // No attributes to validate
            }

            const attributeCombinations = [];
            let hasDuplicates = false;

            // Clear previous error messages
            attributeItems.forEach(item => {
                const existingError = item.querySelector('.duplicate-error');
                if (existingError) {
                    existingError.remove();
                }
                item.classList.remove('border-red-500', 'bg-red-50');
            });

            // Collect all attribute-value combinations
            attributeItems.forEach(item => {
                const attributeSelect = item.querySelector('.attribute-select');
                const valueSelect = item.querySelector('.attribute-value-select');

                if (attributeSelect && valueSelect && attributeSelect.value && valueSelect.value) {
                    const combination = `${attributeSelect.value}-${valueSelect.value}`;
                    console.log('validateVariantAttributes: Checking combination:', combination);

                    if (attributeCombinations.includes(combination)) {
                        console.warn('validateVariantAttributes: Duplicate combination found:', combination);
                        hasDuplicates = true;
                        // Highlight duplicate
                        item.classList.add('border-red-500', 'bg-red-50');

                        // Add error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className =
                            'duplicate-error mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm';
                        errorDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        This attribute and value combination already exists in this variant.
                    </div>
                `;
                        item.appendChild(errorDiv);
                    } else {
                        attributeCombinations.push(combination);
                        console.log('validateVariantAttributes: Added combination:', combination);
                    }
                }
            });

            console.log('validateVariantAttributes: Validation result:', !hasDuplicates, 'Combinations:',
                attributeCombinations);
            return !hasDuplicates;
        }

        // Add attribute to variant
        function addAttributeToVariant(variantId) {
            const attributesContainer = document.querySelector(`.attributes-container[data-variant="${variantId}"]`);
            if (!attributesContainer) {
                console.error('Attributes container not found for variant:', variantId);
                return;
            }

            const attributeCount = attributesContainer.querySelectorAll('.attribute-item').length;

            const attributeHtml = `
    <div class="attribute-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600" data-attribute-index="${attributeCount}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Attribute
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_id]" required>
                    <option value="">Select an attribute</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Value
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-value-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_value_id]" required>
                    <option value="">Select a value</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-3 px-4 rounded-lg transition duration-200 remove-attribute flex items-center justify-center" title="Remove attribute">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>`;

            attributesContainer.insertAdjacentHTML('beforeend', attributeHtml);

            // Validate attributes after adding new one - with proper null checks
            const variantContainer = attributesContainer.closest('[data-variant]');
            if (variantContainer) {
                setTimeout(() => {
                    validateVariantAttributes(variantContainer);
                }, 100);
            }
        }

        // Check if attribute-value combination already exists in variant
        function canAddAttributeCombination(variantContainer, attributeId, valueId) {
            const attributesContainer = variantContainer.querySelector('.attributes-container');
            const attributeItems = attributesContainer.querySelectorAll('.attribute-item');

            for (let item of attributeItems) {
                const existingAttributeSelect = item.querySelector('.attribute-select');
                const existingValueSelect = item.querySelector('.attribute-value-select');

                if (existingAttributeSelect.value == attributeId && existingValueSelect.value == valueId) {
                    return false; // Combination already exists
                }
            }

            return true; // Combination is unique
        }

        // Check if attribute value is already used for any attribute in the variant
        function canAddAttributeValue(variantContainer, valueId, currentAttributeItem = null) {
            const attributesContainer = variantContainer.querySelector('.attributes-container');
            const attributeItems = attributesContainer.querySelectorAll('.attribute-item');

            // Debug log
            console.log('Checking for duplicate value:', valueId, 'Current item:', currentAttributeItem);

            for (let item of attributeItems) {
                // Skip the current attribute item if provided
                if (currentAttributeItem && item === currentAttributeItem) {
                    console.log('Skipping current item');
                    continue;
                }

                const existingValueSelect = item.querySelector('.attribute-value-select');
                const existingValue = existingValueSelect.value;

                console.log('Checking item:', item, 'Existing value:', existingValue);

                // Make sure we're comparing with non-empty values
                if (existingValue && existingValue == valueId) {
                    console.log('Duplicate value found!');
                    return false; // Value already used in another attribute
                }
            }

            console.log('No duplicate found');
            return true; // Value is unique across all attributes
        }

        // Show error message for duplicate attribute
        function showDuplicateAttributeError(button) {
            // Remove any existing error messages
            const existingError = button.closest('.attributes-container').querySelector('.add-attribute-error');
            if (existingError) {
                existingError.remove();
            }

            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className =
                'add-attribute-error mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm';
            errorDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            This attribute and value combination already exists in this variant.
        </div>
    `;

            button.closest('.attributes-container').appendChild(errorDiv);

            // Remove error message after 3 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
        }

        // Show error message for duplicate attribute value
        function showDuplicateAttributeValueError(button) {
            // Remove any existing error messages
            const existingError = button.closest('.attributes-container').querySelector('.duplicate-value-error');
            if (existingError) {
                existingError.remove();
            }

            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className =
                'duplicate-value-error mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm';
            errorDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            This attribute value is already used in another attribute within this variant. Please select a different value.
        </div>
    `;

            button.closest('.attributes-container').appendChild(errorDiv);

            // Remove error message after 3 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
        }

        // Function to populate variants from old input and display errors
        async function populateVariantsFromOldInput() {
            // Check if oldVariantsData and laravelErrors are available from the global scope
            if (typeof oldVariantsData === 'undefined' || typeof laravelErrors === 'undefined') {
                console.warn('oldVariantsData or laravelErrors not found on window object.');
                return;
            }

            if (oldVariantsData && Object.keys(oldVariantsData).length > 0) {
                // Hide empty state and show variants container if it's hidden
                document.getElementById('emptyVariantsState')?.classList.add('hidden');
                document.getElementById('variantsContainer')?.classList.remove('hidden');

                const variantPromises = [];

                // Iterate over each variant from the old input
                Object.keys(oldVariantsData).forEach((variantKey, index) => {
                    const variantData = oldVariantsData[variantKey];
                    // Add the variant structure to the form
                    addManualVariantToForm(); // This increments variantCount internally

                    // The newly added variant will be the last one in the container
                    const variantsContainer = document.getElementById('variantsContainer');
                    const currentVariantElement = variantsContainer.lastElementChild; // Get the most recently added variant

                    if (currentVariantElement) {
                        // Populate basic fields
                        const skuInput = currentVariantElement.querySelector(`input[name$="[sku]"]`);
                        if (skuInput && variantData.sku) skuInput.value = variantData.sku;

                        const stockInput = currentVariantElement.querySelector(`input[name$="[stock]"]`);
                        if (stockInput && variantData.stock) stockInput.value = variantData.stock;

                        const weightInput = currentVariantElement.querySelector(`input[name$="[weight]"]`);
                        if (weightInput && variantData.weight) weightInput.value = variantData.weight;

                        const buyingPriceInput = currentVariantElement.querySelector(`input[name$="[buying_price]"]`);
                        if (buyingPriceInput && variantData.buying_price) buyingPriceInput.value = variantData.buying_price;

                        const sellPriceInput = currentVariantElement.querySelector(`input[name$="[sell_price]"]`);
                        if (sellPriceInput && variantData.sell_price) sellPriceInput.value = variantData.sell_price;

                        const discountPriceInput = currentVariantElement.querySelector(`input[name$="[discount_price]"]`);
                        if (discountPriceInput && variantData.discount_price) discountPriceInput.value = variantData.discount_price;

                        const statusSelect = currentVariantElement.querySelector(`select[name$="[status]"]`);
                        if (statusSelect && variantData.status) statusSelect.value = variantData.status;

                        // Populate attributes
                        if (variantData.attributes && Object.keys(variantData.attributes).length > 0) {
                            const attributesContainer = currentVariantElement.querySelector('.attributes-container');
                            if (attributesContainer && attributesContainer.children.length > 0 && Object.keys(variantData.attributes).length > 0) {
                                attributesContainer.innerHTML = ''; 
                            }

                            Object.keys(variantData.attributes).forEach((attrKey, attrIndex) => {
                                const attributeData = variantData.attributes[attrKey];
                                
                                if (attrIndex > 0 || (attrIndex === 0 && attributesContainer.children.length === 0)) {
                                    addAttributeToVariant(currentVariantElement.dataset.variant);
                                }
                                
                                const currentAttributeItem = attributesContainer.children[attrIndex];

                                if (currentAttributeItem) {
                                    const attributeSelect = currentAttributeItem.querySelector(`select[name$="[attributes][${attrIndex}][attribute_id]"]`);
                                    const valueSelect = currentAttributeItem.querySelector(`select[name$="[attributes][${attrIndex}][attribute_value_id]"]`);

                                    if (attributeSelect && attributeData.attribute_id) {
                                        attributeSelect.value = attributeData.attribute_id;
                                        
                                        // Create a promise for the attribute value loading
                                        const promise = new Promise((resolve) => {
                                            const eventListener = () => {
                                                // Use a timeout to ensure the DOM is updated after options are loaded
                                                setTimeout(() => {
                                                    if (valueSelect && attributeData.attribute_value_id) {
                                                        valueSelect.value = attributeData.attribute_value_id;
                                                    }
                                                    attributeSelect.removeEventListener('change', eventListener);
                                                    resolve();
                                                }, 100); // Short delay for DOM update
                                            };
                                            attributeSelect.addEventListener('change', eventListener, { once: true });
                                            attributeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                        });
                                        variantPromises.push(promise);
                                    }
                                }
                            });
                        }
                    }
                });
                
                // Wait for all attribute loading promises to resolve
                await Promise.all(variantPromises);
            }

            // Display errors for variants after all populations are complete
            displayVariantErrors();
        }

        // Function to display validation errors for variants
        function displayVariantErrors() {
            console.log('displayVariantErrors called. laravelErrors:', laravelErrors);
            if (!laravelErrors) {
                console.log('No laravelErrors object found.');
                return;
            }

            // Configuration map for finding DOM elements based on error field paths
            const fieldConfig = {
                'sku': { selector: 'input[name$="[sku]"]' },
                'stock': { selector: 'input[name$="[stock]"]' },
                'buying_price': { selector: 'input[name$="[buying_price]"]' },
                'sell_price': { selector: 'input[name$="[sell_price]"]' },
                'discount_price': { selector: 'input[name$="[discount_price]"]' },
                'status': { selector: 'select[name$="[status]"]' },
                'attributes': {
                    // Special handler for attributes, as it requires nested parsing
                    handler: (variantContainer, pathParts) => {
                        const [attrIndex, attrFieldName] = pathParts;
                        const attributeItem = variantContainer.querySelector(`.attributes-container .attribute-item[data-attribute-index="${attrIndex}"]`);
                        if (!attributeItem) return null;
                        return attributeItem.querySelector(`select[name$="[attributes][${attrIndex}][${attrFieldName}]"]`);
                    }
                }
            };

            const variantErrorPattern = /^variants\.(\d+)\.(.+)$/;

            Object.keys(laravelErrors).forEach(errorKey => {
                const match = errorKey.match(variantErrorPattern);
                if (!match) return; // Skip if not a variant error

                const [, variantIndex, fieldPath] = match;
                const variantContainer = document.querySelector(`[data-variant="${variantIndex}"]`);
                if (!variantContainer) {
                    console.warn(`Variant container for index ${variantIndex} not found.`);
                    return;
                }

                const errorMessage = laravelErrors[errorKey][0];
                let targetElement = null;
                
                // Determine the root of the field path (e.g., 'sku', 'attributes')
                const [rootKey, ...pathParts] = fieldPath.split('.');

                if (fieldConfig[rootKey]) {
                    const config = fieldConfig[rootKey];
                    if (config.handler) {
                        // Use the special handler for complex fields like attributes
                        targetElement = config.handler(variantContainer, pathParts);
                    } else if (config.selector) {
                        // Use the standard selector for simple fields
                        targetElement = variantContainer.querySelector(config.selector);
                    }
                }

                if (targetElement) {
                    console.log(`Displaying error for ${fieldPath} in variant ${variantIndex}:`, errorMessage);
                    displayErrorOnField(targetElement, errorMessage);
                } else {
                    console.warn(`Could not find target element for error key: ${errorKey}`);
                }
            });
        }

        // Helper function to display an error message below a field
        function displayErrorOnField(fieldElement, message) {
            // Remove any existing error message for this field
            const existingError = fieldElement.parentNode.querySelector('.field-error-message');
            if (existingError) {
                existingError.remove();
            }

            // Add border-red-500 to the field
            fieldElement.classList.add('border-red-500');

            // Create error div
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error-message text-sm text-red-600 mt-1';
            errorDiv.textContent = message;

            // Insert the error message after the field
            fieldElement.parentNode.insertBefore(errorDiv, fieldElement.nextSibling);
        }

        // Load manual attribute values
        function loadManualAttributeValues(variantIndex) {
            const select = document.querySelector(`select[name="variants[${variantIndex}][attributes][0][attribute_id]"]`);
            const valueSelect = document.querySelector(
                `select[name="variants[${variantIndex}][attributes][0][attribute_value_id]"]`);
            const attrId = select.value;

            valueSelect.innerHTML = '';
            valueSelect.disabled = true;

            if (attrId) {
                // Show loading state
                valueSelect.innerHTML = '<option value="">Loading...</option>';

                // Fetch attribute values via AJAX
                fetch(`/admin/attributes/${attrId}/values`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(values => {
                        valueSelect.innerHTML = '';
                        valueSelect.disabled = false;

                        if (values.length > 0) {
                            values.forEach(v => {
                                const opt = document.createElement('option');
                                opt.value = v.id;
                                opt.text = v.value;
                                valueSelect.appendChild(opt);
                            });
                        } else {
                            valueSelect.innerHTML = '<option value="">No values found</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading attribute values:', error);
                        valueSelect.innerHTML = '<option value="">Error loading values</option>';
                    });
            } else {
                valueSelect.innerHTML = '<option value="">Select Attribute First</option>';
            }
        }
    </script>
