<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredCustomersQuery($request);

        $customers = $query->paginate(15)->withQueryString();
        $customers->setCollection(
            $customers->getCollection()->map(fn (User $user) => $this->formatCustomerRow($user))
        );

        $allClientsCount = User::count();
        $activeProfilesCount = User::where('status', 'Active')->count();
        $totalRevenue = (float) Order::whereNotNull('user_id')->where('status', '!=', 'Cancelled')->sum('total');
        $averageLtv = $allClientsCount > 0 ? round($totalRevenue / $allClientsCount, 2) : 0.0;
        $repeatCount = User::whereHas('orders', fn ($q) => $q->where('status', '!=', 'Cancelled'), '>', 1)->count();
        $repeatRate = $allClientsCount > 0 ? round(($repeatCount / $allClientsCount) * 100, 1) : 0.0;

        return view('admin.customers.index', compact(
            'customers',
            'allClientsCount',
            'activeProfilesCount',
            'averageLtv',
            'repeatRate'
        ));
    }

    public function show($id)
    {
        $user = User::with(['orders.items', 'addresses'])
            ->withCount(['orders as valid_orders_count' => fn ($q) => $q->where('status', '!=', 'Cancelled')])
            ->withSum(['orders as valid_total_spent' => fn ($q) => $q->where('status', '!=', 'Cancelled')], 'total')
            ->findOrFail($id);

        $defaultAddr = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();
        $ordersCount = (int) ($user->valid_orders_count ?? 0);
        $totalSpent = (float) ($user->valid_total_spent ?? 0);

        $customer = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email ?? '—',
            'phone' => $user->phone ?? '—',
            'avatar' => $user->avatar ?? 'admin-assets/assets/images/users/avatar-2.jpg',
            'orders_count' => $ordersCount,
            'total_spent' => $totalSpent,
            'avg_order_value' => $ordersCount > 0 ? round($totalSpent / $ordersCount, 2) : 0.0,
            'city' => $defaultAddr
                ? (trim("{$defaultAddr->thana_name}, {$defaultAddr->district_name}", ', ') ?: 'Bangladesh')
                : 'No address saved',
            'status' => $user->status,
            'join_date' => $user->join_date,
        ];

        $orders = $user->orders->sortByDesc('placed_at')->map(fn (Order $o) => $o->toViewArray())->values()->all();
        $addresses = $user->addresses->toArray();

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "Customer status updated to {$newStatus}.");
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filteredCustomersQuery($request)->get()->map(fn (User $u) => $this->formatCustomerRow($u));

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Location', 'Orders', 'Total Spent', 'Status', 'Joined']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['phone'],
                    $row['city'],
                    $row['orders_count'],
                    number_format($row['total_spent'], 2, '.', ''),
                    $row['status'],
                    $row['join_date'],
                ]);
            }

            fclose($handle);
        }, 'customers-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredCustomersQuery(Request $request)
    {
        $query = User::withCount(['orders as orders_count' => fn ($q) => $q->where('status', '!=', 'Cancelled')])
            ->withSum(['orders as total_spent' => fn ($q) => $q->where('status', '!=', 'Cancelled')], 'total')
            ->with('addresses')
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['Active', 'Inactive'], true)) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    private function formatCustomerRow(User $user): array
    {
        $defaultAddr = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();
        $city = $defaultAddr
            ? (trim("{$defaultAddr->thana_name}, {$defaultAddr->district_name}", ', ') ?: 'Bangladesh')
            : 'No address saved';

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email ?? '—',
            'phone' => $user->phone ?? '—',
            'avatar' => $user->avatar ?? 'admin-assets/assets/images/users/avatar-2.jpg',
            'orders_count' => (int) ($user->orders_count ?? 0),
            'total_spent' => (float) ($user->total_spent ?? 0),
            'city' => $city,
            'status' => $user->status,
            'join_date' => $user->join_date,
        ];
    }
}
