@extends('admin.layouts.app')

@section('title', 'Flash Sale Details')

@section('content')
<div class="p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Flash Sale Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.flash-sales.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Flash Sale Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Flash Sale Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                        <p class="text-base text-gray-900 font-semibold">{{ $flashSale->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Slug</label>
                        <p class="text-base text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $flashSale->slug }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $flashSale->status === 'active' ? 'bg-green-100 text-green-800' : ($flashSale->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($flashSale->status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Start Date</label>
                        <p class="text-base text-gray-900">{{ $flashSale->start_date->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">End Date</label>
                        <p class="text-base text-gray-900">{{ $flashSale->end_date->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($flashSale->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-base text-gray-900">{{ $flashSale->description }}</p>
                    </div>
                @endif

                @if($flashSale->banner_image)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Banner Image</label>
                        <div class="mt-2">
                            <img src="{{ $flashSale->banner_image }}" alt="{{ $flashSale->name }}" class="max-w-xs h-auto rounded-lg shadow-md">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ $flashSale->banner_image }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-base text-gray-900">{{ $flashSale->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Updated At</label>
                        <p class="text-base text-gray-900">{{ $flashSale->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Products in this Flash Sale</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $flashSale->products->count() }} {{ $flashSale->products->count() == 1 ? 'Product' : 'Products' }}
                    </span>
                </div>

                @if($flashSale->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Original Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($flashSale->products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($product->firstImage)
                                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->firstImage->getImageUrl() }}" alt="{{ $product->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $product->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ number_format($product->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->pivot->discount_price)
                                                <div class="text-sm text-green-600 font-medium">
                                                    {{ $currency_sign }}{{ number_format($product->pivot->discount_price, 2) }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Save {{ $currency_sign }}{{ number_format($product->pivot->discount_price, 2) }}
                                                </div>
                                            @elseif($product->pivot->discount_percentage)
                                                <div class="text-sm text-green-600 font-medium">
                                                    {{ $product->pivot->discount_percentage }}%
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Save {{ $currency_sign }}{{ number_format($product->price * ($product->pivot->discount_percentage / 100), 2) }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">No discount</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->pivot->discount_price)
                                                <div class="text-sm font-medium text-green-600">{{ $currency_sign }}{{ number_format($product->price - $product->pivot->discount_price, 2) }}</div>
                                            @elseif($product->pivot->discount_percentage)
                                                <div class="text-sm font-medium text-green-600">{{ $currency_sign }}{{ number_format($product->price * (1 - $product->pivot->discount_percentage / 100), 2) }}</div>
                                            @else
                                                <div class="text-sm text-gray-900">{{ $currency_sign }}{{ number_format($product->price, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->status === 'active')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Summary Section -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 mb-3">Flash Sale Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Products:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $flashSale->products->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Active Products:</span>
                                <span class="font-medium text-green-600 ml-2">{{ $flashSale->products->where('status', 'active')->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Products with Discount:</span>
                                <span class="font-medium text-blue-600 ml-2">{{ $flashSale->products->filter(function($product) { return $product->pivot->discount_price || $product->pivot->discount_percentage; })->count() }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No products assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">This flash sale doesn't have any products assigned to it yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Products
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Duration</span>
                        <span class="text-lg font-semibold text-gray-900">
                            {{ $flashSale->start_date->diffInDays($flashSale->end_date) }} days
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Days Left</span>
                        <span class="text-lg font-semibold text-gray-900">
                            {{ max(0, $flashSale->end_date->diffInDays(now())) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-start">
                        <span class="text-sm text-gray-600">Current Validity</span>
                        <div class="text-right">
                            <span class="text-lg font-semibold {{ $flashSale->isValid() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $flashSale->isValid() ? 'Valid' : 'Invalid' }}
                            </span>
                            @if (!$flashSale->isValid())
                                <div class="text-xs text-red-600 mt-1 max-w-xs text-left">
                                    @if ($flashSale->status !== \App\Constants\FlashSaleStatus::ACTIVE)
                                        • Status is not active<br>
                                    @endif
                                    @if ($flashSale->start_date > now())
                                        • Start date is in the future ({{ $flashSale->start_date->format('M d, Y H:i') }})<br>
                                    @endif
                                    @if ($flashSale->end_date < now())
                                        • End date has passed ({{ $flashSale->end_date->format('M d, Y H:i') }})<br>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Flash Sale
                    </a>
                    
                    @if($flashSale->trashed())
                        <form action="{{ route('admin.flash-sales.restore', $flashSale->id) }}" method="POST" class="inline w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to restore this flash sale?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Restore Flash Sale
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.flash-sales.force-delete', $flashSale->id) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to permanently delete this flash sale? This action cannot be undone.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Permanently
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.flash-sales.destroy', $flashSale->id) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to delete this flash sale?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Flash Sale
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
