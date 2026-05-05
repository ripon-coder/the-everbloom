@extends('admin.layouts.app')
@section('content')
    <!-- Dashboard Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Good afternoon, {{ Auth::guard('admin')->user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Here's what's happening with your store today.</p>
        </div>
    </div>
    <!-- Dashboard Content -->
    <div class="space-y-6">
        <!-- Financial Performance Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- This Month Profit -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    @php
                        $profitDiff = $stats['this_month_profit'] - $stats['last_month_profit'];
                        $profitTrend = $stats['last_month_profit'] > 0 ? ($profitDiff / $stats['last_month_profit']) * 100 : 0;
                    @endphp
                    <span class="text-xs font-medium {{ $profitTrend >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-2 py-1 rounded-full">
                        {{ $profitTrend >= 0 ? '+' : '' }}{{ number_format($profitTrend, 1) }}%
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">This Month Profit</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $currency_sign }}{{ number_format($stats['this_month_profit'], 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Net profit for {{ now()->format('F') }}</p>
                </div>
            </div>

            <!-- Last Month Profit -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Month Profit</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $currency_sign }}{{ number_format($stats['last_month_profit'], 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Net profit for {{ now()->subMonth()->format('F') }}</p>
                </div>
            </div>

            <!-- Total Revenue Card (Duplicate or Move) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">All-time Profit</p>
                    @php $totalProfit = \App\Models\Order::where('payment_status', 'paid')->sum('profit'); @endphp
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $currency_sign }}{{ number_format($totalProfit, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Cumulative net profit</p>
                </div>
            </div>

            <!-- Store Efficiency Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit Margin</p>
                    @php 
                        $margin = $stats['total_revenue'] > 0 ? ($totalProfit / $stats['total_revenue']) * 100 : 0;
                    @endphp
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($margin, 1) }}%</h3>
                    <p class="text-xs text-gray-400 mt-2">Average profitability</p>
                </div>
            </div>
        </div>
        <!-- Key Metrics Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Total Orders Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-full">+8.2%</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Orders processed</p>
                </div>
            </div>

            <!-- Pending Orders Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-full">Action Needed</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_orders'] }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Awaiting processing</p>
                </div>
            </div>

            <!-- Completed Orders Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-full">Healthy</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivered Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_orders'] }}</h3>
                    <p class="text-xs text-gray-400 mt-2">Successfully completed</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profit Analytics Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Profit Analytics</h3>
                        <p class="text-xs text-gray-500">Weekly net earnings performance</p>
                    </div>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-full">Last 7 Days</span>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Order Activity Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order Volume</h3>
                        <p class="text-xs text-gray-500">Distribution of orders over time</p>
                    </div>
                    <span class="text-xs font-medium text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-full">Last 7 Days</span>
                </div>
                <div class="chart-container">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Transactions</h3>
                        <p class="text-xs text-gray-500">Latest orders placed by customers</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th class="pb-3 px-2">Order #</th>
                                <th class="pb-3 px-2">Customer</th>
                                <th class="pb-3 px-2">Amount</th>
                                <th class="pb-3 px-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-4 px-2">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                    </td>
                                    <td class="py-4 px-2">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user->name ?? 'Guest' }}</span>
                                            <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-2">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $currency_sign }}{{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->getStatusColor() }}">
                                            {{ $order->getStatusText() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-500">No recent orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Categories -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Categories</h3>
                    <p class="text-xs text-gray-500">Distribution by product count</p>
                </div>
                <div class="chart-container !h-48">
                    <canvas id="trafficSourcesChart"></canvas>
                </div>
                <div class="mt-6 space-y-3">
                    @php
                        $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500'];
                    @endphp
                    @foreach($stats['top_categories'] as $index => $category)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center">
                            <div class="w-2.5 h-2.5 {{ $colors[$index] ?? 'bg-gray-400' }} rounded-full mr-3 shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $category->name }}</span>
                        </div>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $category->products_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Additional Row for Products & Health -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Performing Products -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Performing Products</h3>
                    <p class="text-xs text-gray-500">Based on sales volume and revenue</p>
                </div>
                <div class="space-y-4">
                    @foreach($stats['top_products'] as $product)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-600 flex items-center justify-center text-blue-600 font-bold shadow-sm text-sm">
                                {{ substr($product->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $product->total_sold }} units sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $currency_sign }}{{ number_format($product->total_revenue, 2) }}</p>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight">Revenue</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Store Health -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Store Health</h3>
                    <p class="text-xs text-gray-500">System performance indicators</p>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Conversion Rate</span>
                            <span class="text-sm font-bold text-blue-600">3.2%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Avg. Order Value</span>
                            <span class="text-sm font-bold text-emerald-600">{{ $currency_sign }}450</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Customer Satisfaction</span>
                            <span class="text-sm font-bold text-amber-500">4.8/5</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                    <div class="pt-4 mt-4 border-t border-gray-50 dark:border-gray-700">
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 italic">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            All systems are operational
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Overview (Condensed) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center md:border-r border-gray-100 dark:border-gray-700 last:border-0">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_products']) }}</p>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Products</p>
                </div>
                <div class="text-center md:border-r border-gray-100 dark:border-gray-700 last:border-0">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_customers']) }}</p>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Customers</p>
                </div>
                <div class="text-center md:border-r border-gray-100 dark:border-gray-700 last:border-0">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_categories']) }}</p>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Categories</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_brands']) }}</p>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Brands</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js default configuration
            Chart.defaults.font.family = "'Inter', 'system-ui', '-apple-system', 'sans-serif'";
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: 'bold' };

            // Revenue Chart - Line Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 400);
            revenueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
            revenueGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['chart_labels']) !!},
                    datasets: [
                        {
                            label: 'Profit',
                            data: {!! json_encode($stats['profit_chart_data']) !!},
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10B981',
                            pointBorderWidth: 2,
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
                            grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                            ticks: {
                                callback: function(value) {
                                    return '{{ $currency_sign }}' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // Order Activity Chart - Bar Chart
            const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
            new Chart(userActivityCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['chart_labels']) !!},
                    datasets: [{
                        label: 'Orders',
                        data: {!! json_encode($stats['order_chart_data']) !!},
                        backgroundColor: 'rgba(99, 102, 241, 0.85)',
                        hoverBackgroundColor: 'rgba(99, 102, 241, 1)',
                        borderRadius: 6,
                        barThickness: 20,
                    }]
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
                            grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Traffic Sources Chart - Doughnut Chart (Now Top Categories)
            const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
            new Chart(trafficSourcesCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($stats['top_categories']->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($stats['top_categories']->pluck('products_count')) !!},
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6',
                            '#F59E0B'
                        ],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '75%'
                }
            });
        });
    </script>
@endsection
