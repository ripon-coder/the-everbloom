<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storefront\CheckoutRequest;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Order;
use App\Models\Thana;
use App\Services\Checkout\PlaceOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $districts = District::active()->get(['id', 'name', 'bn_name', 'delivery_charge']);

        $isGuest = !Auth::guard('web')->check();
        $savedAddresses = [];

        $prefill = [
            'email' => '',
            'phone' => '',
            'firstName' => '',
            'lastName' => '',
            'country' => 'Bangladesh',
            'district_id' => '',
            'thana_id' => '',
            'area_id' => '',
            'address' => '',
            'addressLine2' => '',
            'saveAddress' => false,
        ];

        if (!$isGuest) {
            $user = Auth::guard('web')->user();
            $dbAddresses = $user->addresses()
                ->with(['district', 'thana', 'area'])
                ->orderByDesc('is_default')
                ->get();

            if ($dbAddresses->isNotEmpty()) {
                $savedAddresses = $dbAddresses->map(function ($addr, $idx) use ($user) {
                    $recipient = trim(($addr->first_name ?? '') . ' ' . ($addr->last_name ?? ''));
                    if ($recipient === '') {
                        $recipient = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    }

                    return [
                        'id' => 'db_' . $addr->id,
                        'label' => $addr->label ?: ($idx === 0 ? 'Home' : 'Address ' . ($idx + 1)),
                        'is_default' => (bool) $addr->is_default,
                        'recipient_name' => $recipient ?: 'Customer',
                        'first_name' => $addr->first_name ?: ($user->first_name ?? ''),
                        'last_name' => $addr->last_name ?: ($user->last_name ?? ''),
                        'phone' => $addr->phone ?: ($user->phone ?? ''),
                        'country' => 'Bangladesh',
                        'district_id' => $addr->district_id ? (string) $addr->district_id : '',
                        'thana_id' => $addr->thana_id ? (string) $addr->thana_id : '',
                        'area_id' => $addr->area_id ? (string) $addr->area_id : '',
                        'district_name' => $addr->district?->name ?? '',
                        'thana_name' => $addr->thana?->name ?? '',
                        'area_name' => $addr->area?->name ?? '',
                        'address' => $addr->address_line1 ?? '',
                        'address_line2' => $addr->address_line2 ?? '',
                    ];
                })->values()->all();
            } else {
                $demoAddresses = config('demo.account.addresses', []);
                $savedAddresses = collect($demoAddresses)->map(function ($addr, $idx) {
                    $district = !empty($addr['district'])
                        ? District::where('name', $addr['district'])->first()
                        : null;
                    $thana = ($district && !empty($addr['thana']))
                        ? Thana::where('district_id', $district->id)->where('name', $addr['thana'])->first()
                        : null;
                    $area = ($thana && !empty($addr['area']))
                        ? Area::where('thana_id', $thana->id)->where('name', $addr['area'])->first()
                        : null;

                    $recipient = $addr['recipient_name']
                        ?? trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? ''));

                    return [
                        'id' => $addr['id'] ?? ('demo_' . ($idx + 1)),
                        'label' => $addr['label'] ?? ($idx === 0 ? 'Home' : 'Address ' . ($idx + 1)),
                        'is_default' => (bool) ($addr['is_default'] ?? false),
                        'recipient_name' => $recipient ?: 'Customer',
                        'first_name' => $addr['first_name'] ?? '',
                        'last_name' => $addr['last_name'] ?? '',
                        'phone' => $addr['phone'] ?? '',
                        'country' => $addr['country'] ?? 'Bangladesh',
                        'district_id' => $district ? (string) $district->id : '',
                        'thana_id' => $thana ? (string) $thana->id : '',
                        'area_id' => $area ? (string) $area->id : '',
                        'district_name' => $district?->name ?? ($addr['district'] ?? ''),
                        'thana_name' => $thana?->name ?? ($addr['thana'] ?? ''),
                        'area_name' => $area?->name ?? ($addr['area'] ?? ''),
                        'address' => $addr['address_line1'] ?? ($addr['address'] ?? ''),
                        'address_line2' => $addr['address_line2'] ?? '',
                    ];
                })->values()->all();
            }

            $defaultAddr = collect($savedAddresses)->firstWhere('is_default', true) ?? ($savedAddresses[0] ?? null);

            $prefill['email'] = $user->email ?? '';
            $prefill['phone'] = $defaultAddr['phone'] ?? ($user->phone ?? '');
            $prefill['firstName'] = $defaultAddr['first_name'] ?? ($user->first_name ?? '');
            $prefill['lastName'] = $defaultAddr['last_name'] ?? ($user->last_name ?? '');

            if ($defaultAddr) {
                $prefill['district_id'] = $defaultAddr['district_id'] ?? '';
                $prefill['thana_id'] = $defaultAddr['thana_id'] ?? '';
                $prefill['area_id'] = $defaultAddr['area_id'] ?? '';
                $prefill['address'] = $defaultAddr['address'] ?? '';
                $prefill['addressLine2'] = $defaultAddr['address_line2'] ?? '';
            }
        }

        return view('frontend.checkout.index', [
            'prefillForm' => $prefill,
            'districts' => $districts,
            'savedAddresses' => $savedAddresses,
            'isGuest' => $isGuest,
        ]);
    }

    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $subtotal = (float) ($validated['subtotal'] ?? 0);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValidForSubtotal($subtotal)) {
            return response()->json([
                'valid' => false,
                'message' => 'This promotional code is invalid, expired, or does not meet order requirements.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'label' => $coupon->label,
        ]);
    }

    public function store(CheckoutRequest $request, PlaceOrderService $placeOrderService): JsonResponse
    {
        $order = $placeOrderService->placeOrder(
            validated: $request->validated(),
            user: Auth::guard('web')->user(),
        );

        if (! Auth::guard('web')->check()) {
            session(['recent_order_number' => $order->order_number]);
        }

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'redirect_url' => route('checkout.confirmation', ['orderNumber' => $order->order_number]),
        ]);
    }

    public function confirmation(Request $request, string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        $user = Auth::guard('web')->user();
        $isOwner = $user && $order->user_id === $user->id;
        $isRecentGuestOrder = session('recent_order_number') === $order->order_number;

        abort_unless($isOwner || $isRecentGuestOrder || $request->hasValidSignature(), 403);

        return view('frontend.checkout.confirmation', compact('order'));
    }
}
