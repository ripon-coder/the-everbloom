<!-- Orders Section -->
<div class="space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-gray-200">
        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Order History</h2>
        <span class="text-xs text-gray-500 font-medium">{{ $orders->total() }} Total Orders</span>
    </div>
    
    <!-- Orders List with Gap -->
    <div class="space-y-3.5">
        @forelse($orders as $order)
            <div class="bg-white border border-gray-200 p-4 sm:p-5 hover:border-gray-300 hover:shadow-sm transition-all rounded-none flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <!-- Left: Order Info -->
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-gray-900 text-sm">#{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 {{ $order->getStatusColor() }} text-[10px] font-bold uppercase tracking-wider rounded-none">
                            {{ $order->getStatusText() }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                </div>

                <!-- Right: Total & Action -->
                <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block sm:inline">Total: </span>
                        <span class="font-black text-gray-900 text-sm">Tk. {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <a href="{{ route('account.order.show', $order->order_number) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-900 hover:text-white hover:bg-primary uppercase tracking-wider border border-gray-300 hover:border-primary px-3.5 py-2 transition-colors bg-gray-50 rounded-none">
                        <span>View Details</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 p-12 text-center text-gray-500 italic text-sm rounded-none">
                No orders found yet.
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $orders->links() }}
        </div>
    @endif
</div>
