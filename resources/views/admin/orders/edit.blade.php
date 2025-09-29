@extends('admin.layouts.app')

@section('title', 'Edit Order - ' . $order->order_number)

@section('content')
    <div class="p-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Order</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $order->order_number }}
                </span>
            </div>
        </div>

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                                <select id="user_id" name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a customer</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                            <button type="button" id="addProductBtn" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Product
                            </button>
                        </div>
                        
                        <div id="productsContainer" class="space-y-4">
                            @foreach($order->orderProducts as $index => $orderProduct)
                                <div class="product-item border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="font-medium text-gray-900">Product</h3>
                                        <button type="button" class="remove-product text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                            <select class="product-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                                <option value="">Select a product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $orderProduct->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} - ${{ number_format($product->price, 2) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                            <input type="number" class="product-quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" value="{{ $orderProduct->quantity }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                                            <input type="text" class="product-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly value="${{ number_format($orderProduct->total_price, 2) }}">
                                        </div>
                                    </div>
                                    <input type="hidden" class="product-id" name="products[{{ $index }}][product_id]" value="{{ $orderProduct->product_id }}">
                                    <input type="hidden" class="product-unit-price" name="products[{{ $index }}][unit_price]" value="{{ $orderProduct->unit_price }}">
                                    <input type="hidden" class="product-total-price" name="products[{{ $index }}][total_price]" value="{{ $orderProduct->total_price }}">
                                    @if($orderProduct->product_variant_id)
                                        <input type="hidden" name="products[{{ $index }}][product_variant_id]" value="{{ $orderProduct->product_variant_id }}">
                                    @endif
                                    @if($orderProduct->notes)
                                        <div class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                            <input type="text" name="products[{{ $index }}][notes]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $orderProduct->notes }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                    <input type="text" id="subtotal" name="subtotal" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" value="{{ number_format($order->subtotal, 2) }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                                    <input type="text" id="total_amount" name="total_amount" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" value="{{ number_format($order->total_amount, 2) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Addresses</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Shipping Address -->
                            <div>
                                <h3 class="text-md font-medium text-gray-900 mb-3">Shipping Address *</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="shipping_address[name]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['name'] ?? '') : '' }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea name="shipping_address[address]" required rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? '') : '' }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                            <input type="text" name="shipping_address[city]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['city'] ?? '') : '' }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                            <input type="text" name="shipping_address[state]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['state'] ?? '') : '' }}">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                            <input type="text" name="shipping_address[zip]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['zip'] ?? '') : '' }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                            <input type="text" name="shipping_address[country]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['country'] ?? '') : '' }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="text" name="shipping_address[phone]" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->shipping_address) ? ($order->shipping_address['phone'] ?? '') : '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address -->
                            <div>
                                <h3 class="text-md font-medium text-gray-900 mb-3">Billing Address</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="sameAsShipping" class="mr-2">
                                        <label for="sameAsShipping" class="text-sm text-gray-700">Same as shipping address</label>
                                    </div>
                                    <div id="billingAddressFields">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                            <input type="text" name="billing_address[name]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['name'] ?? '') : '' }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                            <textarea name="billing_address[address]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ is_array($order->billing_address) ? ($order->billing_address['address'] ?? '') : '' }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                                <input type="text" name="billing_address[city]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['city'] ?? '') : '' }}">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                                <input type="text" name="billing_address[state]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['state'] ?? '') : '' }}">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                                <input type="text" name="billing_address[zip]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['zip'] ?? '') : '' }}">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                                <input type="text" name="billing_address[country]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['country'] ?? '') : '' }}">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                            <input type="text" name="billing_address[phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ is_array($order->billing_address) ? ($order->billing_address['phone'] ?? '') : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select payment method</option>
                                    <option value="cash_on_delivery" {{ $order->payment_method === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                    <option value="credit_card" {{ $order->payment_method === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="paypal" {{ $order->payment_method === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bank_transfer" {{ $order->payment_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($status_options as $key => $value)
                                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                                <select id="payment_status" name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($payment_status_options as $key => $value)
                                        <option value="{{ $key }}" {{ $order->payment_status === $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any additional notes about this order...">{{ $order->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium" id="summarySubtotal">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount</label>
                                <input type="number" id="discount_amount" name="discount_amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ number_format($order->discount_amount, 2) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tax Amount</label>
                                <input type="number" id="tax_amount" name="tax_amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ number_format($order->tax_amount, 2) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Amount</label>
                                <input type="number" id="shipping_amount" name="shipping_amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ number_format($order->shipping_amount, 2) }}">
                            </div>
                            <div class="border-t pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-base font-semibold text-gray-900">Total:</span>
                                    <span class="text-base font-bold text-gray-900" id="summaryTotal">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('admin.orders.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                            Update Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Product Template (Hidden) -->
    <div id="productTemplate" class="hidden">
        <div class="product-item border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-gray-900">Product</h3>
                <button type="button" class="remove-product text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select class="product-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} - ${{ number_format($product->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" class="product-quantity w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                    <input type="text" class="product-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                </div>
            </div>
            <input type="hidden" class="product-id" name="products[0][product_id]">
            <input type="hidden" class="product-unit-price" name="products[0][unit_price]">
            <input type="hidden" class="product-total-price" name="products[0][total_price]">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let productIndex = {{ $order->orderProducts->count() }};
            const productsContainer = document.getElementById('productsContainer');
            const productTemplate = document.getElementById('productTemplate');
            const addProductBtn = document.getElementById('addProductBtn');

            // Add product functionality
            addProductBtn.addEventListener('click', function() {
                const template = productTemplate.cloneNode(true);
                template.id = '';
                template.classList.remove('hidden');
                
                // Update names and IDs
                const productItem = template.querySelector('.product-item');
                const inputs = productItem.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace('products[0]', `products[${productIndex}]`);
                    }
                });

                // Add event listeners
                const productSelect = template.querySelector('.product-select');
                const quantityInput = template.querySelector('.product-quantity');
                const totalInput = template.querySelector('.product-total');
                const productIdInput = template.querySelector('.product-id');
                const unitPriceInput = template.querySelector('.product-unit-price');
                const totalPriceInput = template.querySelector('.product-total-price');

                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    const total = price * quantity;

                    unitPriceInput.value = price;
                    totalInput.value = '$' + total.toFixed(2);
                    totalPriceInput.value = total;
                    productIdInput.value = this.value;
                    updateOrderTotals();
                });

                quantityInput.addEventListener('input', function() {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const quantity = parseInt(this.value) || 0;
                    const total = price * quantity;

                    totalInput.value = '$' + total.toFixed(2);
                    totalPriceInput.value = total;
                    updateOrderTotals();
                });

                // Remove product functionality
                const removeBtn = template.querySelector('.remove-product');
                removeBtn.addEventListener('click', function() {
                    template.remove();
                    updateOrderTotals();
                });

                productsContainer.appendChild(template);
                productIndex++;
            });

            // Add event listeners to existing products
            document.querySelectorAll('.product-item').forEach(item => {
                const productSelect = item.querySelector('.product-select');
                const quantityInput = item.querySelector('.product-quantity');
                const totalInput = item.querySelector('.product-total');
                const productIdInput = item.querySelector('.product-id');
                const unitPriceInput = item.querySelector('.product-unit-price');
                const totalPriceInput = item.querySelector('.product-total-price');

                if (productSelect) {
                    productSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const price = parseFloat(selectedOption.dataset.price) || 0;
                        const quantity = parseInt(quantityInput.value) || 0;
                        const total = price * quantity;

                        unitPriceInput.value = price;
                        totalInput.value = '$' + total.toFixed(2);
                        totalPriceInput.value = total;
                        productIdInput.value = this.value;
                        updateOrderTotals();
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', function() {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const price = parseFloat(selectedOption.dataset.price) || 0;
                        const quantity = parseInt(this.value) || 0;
                        const total = price * quantity;

                        totalInput.value = '$' + total.toFixed(2);
                        totalPriceInput.value = total;
                        updateOrderTotals();
                    });
                }

                // Remove product functionality
                const removeBtn = item.querySelector('.remove-product');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        item.remove();
                        updateOrderTotals();
                    });
                }
            });

            // Same as shipping address functionality
            const sameAsShippingCheckbox = document.getElementById('sameAsShipping');
            const billingAddressFields = document.getElementById('billingAddressFields');

            sameAsShippingCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    billingAddressFields.style.opacity = '0.5';
                    billingAddressFields.style.pointerEvents = 'none';
                    
                    // Copy shipping address to billing address
                    const shippingInputs = document.querySelectorAll('input[name^="shipping_address"]');
                    shippingInputs.forEach(input => {
                        const billingInput = document.querySelector(`input[name="billing_address[${input.name.split('[')[1].split(']')[0]}"]`);
                        if (billingInput) {
                            billingInput.value = input.value;
                        }
                    });
                } else {
                    billingAddressFields.style.opacity = '1';
                    billingAddressFields.style.pointerEvents = 'auto';
                }
            });

            // Update order totals
            function updateOrderTotals() {
                let subtotal = 0;
                const productItems = productsContainer.querySelectorAll('.product-item');
                
                productItems.forEach(item => {
                    const totalInput = item.querySelector('.product-total-price');
                    const total = parseFloat(totalInput.value) || 0;
                    subtotal += total;
                });

                const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
                const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
                const shippingAmount = parseFloat(document.getElementById('shipping_amount').value) || 0;
                const total = subtotal - discountAmount + taxAmount + shippingAmount;

                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('total_amount').value = total.toFixed(2);
                document.getElementById('summarySubtotal').textContent = '$' + subtotal.toFixed(2);
                document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
            }

            // Add event listeners for amount inputs
            ['discount_amount', 'tax_amount', 'shipping_amount'].forEach(id => {
                document.getElementById(id).addEventListener('input', updateOrderTotals);
            });
        });
    </script>
@endsection
