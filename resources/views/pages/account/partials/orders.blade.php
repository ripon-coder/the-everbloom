<!-- Orders Tab -->
<div x-show="activeTab === 'orders'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-5 md:p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Order History</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">#EVB-89012</td>
                        <td class="px-6 py-4">Oct 24, 2023</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Completed</span></td>
                        <td class="px-6 py-4 font-bold text-gray-900">৳ 4,310.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">#EVB-88743</td>
                        <td class="px-6 py-4">Sep 12, 2023</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Processing</span></td>
                        <td class="px-6 py-4 font-bold text-gray-900">৳ 1,250.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">#EVB-85210</td>
                        <td class="px-6 py-4">Jul 05, 2023</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-200 text-gray-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Cancelled</span></td>
                        <td class="px-6 py-4 font-bold text-gray-900">৳ 3,800.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
