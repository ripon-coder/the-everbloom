<section class="max-w-[1400px] mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <h2 class="text-white bg-red-600 px-6 py-2 rounded-full font-bold text-lg inline-block shadow-sm">Featured Categories</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
        @php
            $categories = [
                ['name' => 'AC', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'],
                ['name' => 'Bedsheet', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                ['name' => 'Blender', 'icon' => 'M12 2v2M9 6v12a2 2 0 002 2h2a2 2 0 002-2V6l2-2H7l2 2zM12 10v4'],
                ['name' => 'Body Lotion', 'icon' => 'M7 21h10M9 21V7l2-4h2l2 4v14M12 11v4'],
                ['name' => 'Charging', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['name' => 'Drill Machine', 'icon' => 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z M14 3v5h5 M10 12v6 M8 15h4'],
                ['name' => 'Feature Phone', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z M8 14h8 M8 10h8 M8 6h8'],
                ['name' => 'Gaming', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['name' => 'Gas Stove', 'icon' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5zm6 4h2v2h-2V9zm-4 0h2v2H7V9zm8 0h2v2h-2V9zm-8 4h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2zm-8 4h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2z'],
                ['name' => 'Hand Blender', 'icon' => 'M12 2v2M9 6v12a2 2 0 002 2h2a2 2 0 002-2V6l2-2H7l2 2zM12 10v4'],
                ['name' => 'Hair Trimmer', 'icon' => 'M10 2h4l2 6v12a2 2 0 01-2 2h-4a2 2 0 01-2-2V8l2-6zM10 8h4M10 12h4M10 16h4'],
                ['name' => 'Home Appliances', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['name' => 'Keyboard', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                ['name' => 'Power Bank', 'icon' => 'M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm5 4h4v2h-4V7zm0 4h4v2h-4v-2zm0 4h4v2h-4v-2z'],
                ['name' => 'Smart Watch', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['name' => 'Speaker', 'icon' => 'M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z'],
            ];
        @endphp

        @foreach($categories as $category)
            <a href="#" class="bg-white rounded-lg p-3 flex flex-col items-center justify-center gap-2 border border-gray-200 hover:border-red-500 hover:shadow-md transition-all group h-24">
                <div class="w-8 h-8 flex items-center justify-center text-slate-800 group-hover:text-red-600 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $category['icon'] }}"></path>
                    </svg>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 group-hover:text-red-600 whitespace-nowrap">{{ $category['name'] }}</span>
            </a>
        @endforeach
    </div>
</section>
