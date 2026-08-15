@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <!-- Dashboard Header -->
    <div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Welcome back, {{ Auth::guard('admin')->user()->name }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Store Active</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Here is the real-time summary of store orders, revenue, and inventory performance.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Product</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition border border-gray-200">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Manage Orders</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition border border-gray-300">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Visit Store</span>
            </a>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="space-y-6">
        
        <!-- Primary KPI Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Sales Volume -->
            <div class="bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Sales Volume</span>
                        <div class="w-8 h-8 bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currency_sign }}{{ number_format($stats['total_sales_volume'], 2) }}</h2>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Paid Revenue:</span>
                    <span class="font-bold text-emerald-600">{{ $currency_sign }}{{ number_format($stats['paid_revenue'], 2) }}</span>
                </div>
            </div>

            <!-- Card 2: Total Orders -->
            <div class="bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Orders</span>
                        <div class="w-8 h-8 bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_orders']) }}</h2>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Pending Action:</span>
                    <span class="font-bold text-amber-600">{{ $stats['pending_orders'] }} orders</span>
                </div>
            </div>

            <!-- Card 3: Average Order Value (AOV) -->
            <div class="bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Order Value</span>
                        <div class="w-8 h-8 bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currency_sign }}{{ number_format($stats['avg_order_value'], 2) }}</h2>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Delivered Orders:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $stats['completed_orders'] }}</span>
                </div>
            </div>

            <!-- Card 4: Customer Satisfaction & Rating -->
            <div class="bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Customer Rating</span>
                        <div class="w-8 h-8 bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['avg_rating'], 1) }}</h2>
                        <span class="text-xs text-amber-500 font-semibold">/ 5.0 ★</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Approved Reviews:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $stats['total_reviews'] }}</span>
                </div>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- 7-Day Performance Trend Chart -->
            <div class="lg:col-span-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">7-Day Sales & Order Activity</h3>
                        <p class="text-xs text-gray-500">Daily order volume over the last 7 days</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200">Last 7 Days</span>
                </div>
                <div class="h-64 sm:h-72">
                    <canvas id="weeklyActivityChart"></canvas>
                </div>
            </div>

            <!-- Order Status Breakdown Doughnut Chart -->
            <div class="lg:col-span-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Order Status Distribution</h3>
                        <span class="text-xs font-semibold text-gray-500">Real-time</span>
                    </div>
                    <div class="h-44 sm:h-48 my-2">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-amber-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-gray-400">Pending: <strong>{{ $stats['pending_orders'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-blue-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-gray-400">Processing: <strong>{{ $stats['processing_orders'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 inline-block"></span>
                        <span class="text-gray-600 dark:text-gray-400">Delivered: <strong>{{ $stats['completed_orders'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-slate-400 inline-block"></span>
                        <span class="text-gray-600 dark:text-gray-400">Incomplete: <strong>{{ $stats['incomplete_orders'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions & Store Summary Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Recent Orders Table -->
            <div class="lg:col-span-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Recent Orders</h3>
                        <p class="text-xs text-gray-500">Latest transactions placed on your store</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-600 hover:underline uppercase tracking-wider">View All Orders &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 text-gray-500 uppercase font-semibold border-b border-gray-200">
                                <th class="p-3">Order #</th>
                                <th class="p-3">Customer</th>
                                <th class="p-3">Total Amount</th>
                                <th class="p-3">Payment</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition">
                                    <td class="p-3 font-mono font-bold text-gray-900 dark:text-white">
                                        #{{ $order->order_number }}
                                    </td>
                                    <td class="p-3">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $order->user->name ?? 'Guest Customer' }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $order->created_at ? $order->created_at->format('M d, Y · H:i') : '' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 font-bold text-gray-900 dark:text-white">
                                        {{ $currency_sign }}{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($order->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase {{ $order->getStatusColor() }}">
                                            {{ $order->getStatusText() }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-[11px] transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500 italic">No recent orders recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Store Overview & Inventory Summary -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- System & Inventory Health -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Store Health & Inventory</h3>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block text-sm">{{ number_format($stats['total_products']) }}</span>
                                <span class="text-gray-500 text-[11px]">Total Products</span>
                            </div>
                            <span class="px-2 py-0.5 font-bold text-[10px] bg-blue-100 text-blue-800">{{ $stats['active_products'] }} Active</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block text-sm">{{ number_format($stats['total_customers']) }}</span>
                                <span class="text-gray-500 text-[11px]">Registered Customers</span>
                            </div>
                            <a href="{{ route('admin.customers.index') }}" class="text-blue-600 font-bold hover:underline">View &rarr;</a>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block text-sm">{{ number_format($stats['total_categories']) }}</span>
                                <span class="text-gray-500 text-[11px]">Categories</span>
                            </div>
                            <span class="text-gray-700 font-semibold">{{ number_format($stats['total_brands']) }} Brands</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-amber-50/80 border border-amber-200">
                            <div>
                                <span class="font-bold text-amber-900 block text-sm">{{ number_format($stats['low_stock_products']) }}</span>
                                <span class="text-amber-700 text-[11px]">Low Stock Items (&le; 10)</span>
                            </div>
                            @if($stats['low_stock_products'] > 0)
                                <span class="px-2 py-0.5 font-bold text-[10px] bg-amber-200 text-amber-900">Check Stock</span>
                            @else
                                <span class="px-2 py-0.5 font-bold text-[10px] bg-emerald-100 text-emerald-800">Healthy</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Top Categories Progress -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-xs">
                    <div class="mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Top Product Categories</h3>
                        <p class="text-xs text-gray-500">Catalog distribution</p>
                    </div>
                    <div class="space-y-3.5 text-xs">
                        @forelse($stats['top_categories'] as $cat)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $cat->name }}</span>
                                    <span class="font-semibold text-gray-500">{{ $cat->products_count }} items</span>
                                </div>
                                @php
                                    $catPercent = $stats['total_products'] > 0 ? min(100, round(($cat->products_count / $stats['total_products']) * 100)) : 0;
                                @endphp
                                <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5">
                                    <div class="bg-blue-600 h-1.5" style="width: {{ max(5, $catPercent) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 italic text-center py-4">No categories created yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
            Chart.defaults.color = '#64748b';

            // Weekly Orders / Activity Line Chart
            const weeklyCtx = document.getElementById('weeklyActivityChart').getContext('2d');
            new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['chart_labels']) !!},
                    datasets: [
                        {
                            label: 'Orders Count',
                            data: {!! json_encode($stats['order_chart_data']) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Order Status Distribution Doughnut Chart
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Processing', 'Delivered', 'Incomplete', 'Canceled'],
                    datasets: [{
                        data: [
                            {{ $stats['status_counts']['pending'] }},
                            {{ $stats['status_counts']['processing'] }},
                            {{ $stats['status_counts']['delivered'] }},
                            {{ $stats['status_counts']['incomplete'] }},
                            {{ $stats['status_counts']['canceled'] }}
                        ],
                        backgroundColor: [
                            '#f59e0b', // Pending (amber)
                            '#3b82f6', // Processing (blue)
                            '#10b981', // Delivered (emerald)
                            '#94a3b8', // Incomplete (slate)
                            '#ef4444'  // Canceled (red)
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '72%'
                }
            });
        });
    </script>
@endsection
