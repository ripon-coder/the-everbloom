<!-- Orders Section -->
<div class="space-y-6">
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="p-5 md:p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Order History</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Order ID</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Total</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3.5 font-bold text-gray-900">#{{ $order->order_number }}</td>
                            <td class="px-6 py-3.5">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 {{ $order->getStatusColor() }} text-[10px] font-bold uppercase tracking-wider">
                                    {{ $order->getStatusText() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">Tk. {{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('account.order.show', $order->order_number) }}" class="text-primary hover:text-primary-dark font-bold text-xs uppercase tracking-wider">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                No orders found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
