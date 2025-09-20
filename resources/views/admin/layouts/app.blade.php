<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - {{ config('app.name') }}</title>
    <meta name="description" content="Professional analytics dashboard with real-time data visualization">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    
    <!-- Custom Analytics Styles -->
    <style>
        .chart-container {
            position: relative;
            height: 300px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .metric-value {
            font-variant-numeric: tabular-nums;
        }
        .sidebar-gradient {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
        }
        /* Ensure proper z-index layering */
        .navbar-z {
            z-index: 50;
        }
        .sidebar-z {
            z-index: 40;
        }
        .content-z {
            z-index: 30;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navbar -->
    @include('admin.layouts.nav')

    <!-- Sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Main content wrapper -->
    <div id="main-content" class="sm:ml-64 pt-16 min-h-screen content-z relative">
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
                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                            <option>This year</option>
                        </select>
                        <!-- Export Button -->
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    
    <!-- Simple Mobile Sidebar Toggle -->
    <script>
        // Mobile sidebar toggle (hamburger menu)
        document.addEventListener('DOMContentLoaded', () => {
            const mobileToggleBtn = document.querySelector('[data-drawer-toggle="sidebar-multi-level-sidebar"]');
            const sidebar = document.getElementById('sidebar');
            
            if (mobileToggleBtn && sidebar) {
                mobileToggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Mobile sidebar toggle clicked');
                    
                    sidebar.classList.toggle('show');
                    console.log('Sidebar show class:', sidebar.classList.contains('show'));
                });
            }
        });
    </script>

    <!-- Vite JS (Load after other scripts) -->
    @vite(['resources/js/app.js'])
</body>
</html>
