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


        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    </div>

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
