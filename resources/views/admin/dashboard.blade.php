@extends('admin.layouts.app')
@section('content')
    <!-- Dashboard Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Real-time insights and performance metrics</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Date Range Selector -->
                    <select
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>This year</option>
                    </select>
                    <!-- Export Button -->
                    <button
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Content -->
    <div class="space-y-6">
        <!-- Key Metrics Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue Card -->
            <div
                class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Revenue</p>
                        <p class="text-3xl font-bold metric-value">$45,231</p>
                        <p class="text-blue-100 text-xs mt-1">
                            <span class="text-green-300">↑ 12.5%</span> from last month
                        </p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                            </path>
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div
                class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Active Users</p>
                        <p class="text-3xl font-bold metric-value">2,350</p>
                        <p class="text-green-100 text-xs mt-1">
                            <span class="text-green-300">↑ 8.2%</span> from last week
                        </p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate Card -->
            <div
                class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Conversion Rate</p>
                        <p class="text-3xl font-bold metric-value">3.24%</p>
                        <p class="text-purple-100 text-xs mt-1">
                            <span class="text-red-300">↓ 2.1%</span> from last month
                        </p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Avg. Session Time Card -->
            <div
                class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Avg. Session</p>
                        <p class="text-3xl font-bold metric-value">5m 42s</p>
                        <p class="text-orange-100 text-xs mt-1">
                            <span class="text-green-300">↑ 15.3%</span> from last week
                        </p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Overview</h3>
                    <select
                        class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- User Activity Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Activity</h3>
                    <div class="flex space-x-2">
                        <button
                            class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">Daily</button>
                        <button
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Weekly</button>
                        <button
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Monthly</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Traffic Sources -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Traffic Sources</h3>
                <div class="chart-container">
                    <canvas id="trafficSourcesChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Organic Search</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">45%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Direct</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">25%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Social Media</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">20%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Referral</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">10%</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">New user registration</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">John Doe registered 2 minutes ago</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">2 min ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Payment received</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">$1,250.00 from Premium Plan</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">15 min ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Server alert</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">High CPU usage detected</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">New order</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Order #1234 placed</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Server Response</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">125ms</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Database Load</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">67%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 67%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Memory Usage</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">4.2GB / 8GB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 52%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Network I/O</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">234 Mbps</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Data Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Real-time Analytics</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Live</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600 metric-value" id="liveUsers">1,247</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Live Users</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-2xl font-bold text-green-600 metric-value" id="pageViews">8,429</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Page Views</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600 metric-value" id="bounceRate">32.1%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Bounce Rate</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-2xl font-bold text-orange-600 metric-value" id="avgDuration">4m 23s</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Avg. Duration</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js default configuration
            Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
            Chart.defaults.color = '#6B7280';

            // Revenue Chart - Line Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [3200, 4500, 3800, 5200, 4900, 6100, 5800],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // User Activity Chart - Bar Chart
            const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
            new Chart(userActivityCtx, {
                type: 'bar',
                data: {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                    datasets: [{
                        label: 'Active Users',
                        data: [120, 80, 450, 680, 520, 380],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(168, 85, 247)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Traffic Sources Chart - Doughnut Chart
            const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
            new Chart(trafficSourcesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Organic Search', 'Direct', 'Social Media', 'Referral'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#8B5CF6',
                            '#F59E0B'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '70%'
                }
            });

            // Real-time data simulation
            function updateRealTimeData() {
                const liveUsers = document.getElementById('liveUsers');
                const pageViews = document.getElementById('pageViews');
                const bounceRate = document.getElementById('bounceRate');
                const avgDuration = document.getElementById('avgDuration');

                // Simulate real-time updates
                setInterval(() => {
                    // Update live users
                    const currentUsers = parseInt(liveUsers.textContent.replace(',', ''));
                    const newUsers = currentUsers + Math.floor(Math.random() * 20) - 10;
                    liveUsers.textContent = Math.max(1000, newUsers).toLocaleString();

                    // Update page views
                    const currentViews = parseInt(pageViews.textContent.replace(',', ''));
                    const newViews = currentViews + Math.floor(Math.random() * 10);
                    pageViews.textContent = newViews.toLocaleString();

                    // Update bounce rate
                    const currentRate = parseFloat(bounceRate.textContent);
                    const newRate = Math.max(20, Math.min(50, currentRate + (Math.random() * 2 - 1)));
                    bounceRate.textContent = newRate.toFixed(1) + '%';

                    // Update average duration
                    const currentDuration = avgDuration.textContent;
                    const minutes = parseInt(currentDuration);
                    const newMinutes = Math.max(2, Math.min(8, minutes + Math.floor(Math.random() * 3) -
                    1));
                    avgDuration.textContent = newMinutes + 'm ' + Math.floor(Math.random() * 60) + 's';
                }, 3000);
            }

            updateRealTimeData();
        });
    </script>
@endsection
