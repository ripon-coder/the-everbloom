<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Area;
use App\Models\District;
use App\Models\Order;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $recentOrders = $user->orders()
            ->with('items')
            ->latest('placed_at')
            ->take(3)
            ->get()
            ->map(fn (Order $order) => $order->toViewArray())
            ->all();

        $defaultAddress = $user->addresses()->where('is_default', true)->first()
            ?? $user->addresses()->first();

        return view('frontend.account.index', [
            'customer' => $user->toArray(),
            'recentOrders' => $recentOrders,
            'defaultAddress' => $defaultAddress?->toArray(),
        ]);
    }

    public function orders(Request $request): View
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $orders = $request->query('empty')
            ? []
            : $user->orders()
                ->with('items')
                ->latest('placed_at')
                ->get()
                ->map(fn (Order $order) => $order->toViewArray())
                ->all();

        return view('frontend.account.orders', [
            'orders' => $orders,
        ]);
    }

    public function showOrder(string $id): View
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $order = $user->orders()
            ->with('items')
            ->where(function ($query) use ($id) {
                $query->where('order_number', $id);
                if (is_numeric($id)) {
                    $query->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();

        return view('frontend.account.order-details', [
            'order' => $order->toViewArray(),
            'id' => $order->order_number,
        ]);
    }

    public function addresses(): View
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get()
            ->toArray();

        $districts = District::active()->get(['id', 'name', 'bn_name', 'delivery_charge']);

        return view('frontend.account.addresses', [
            'addresses' => $addresses,
            'districts' => $districts,
        ]);
    }

    public function profile(): View
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        return view('frontend.account.profile', [
            'customer' => $user->toArray(),
        ]);
    }

    public function login(): View
    {
        return view('frontend.account.login');
    }

    public function loginSubmit(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'Active',
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('account'));
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function register(): View
    {
        return view('frontend.account.register');
    }

    public function registerSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'Active',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('account');
    }

    public function forgotPassword(): View
    {
        return view('frontend.account.forgot-password');
    }

    public function forgotPasswordSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return back()->with('status', 'If an account matches that email, a password reset link has been sent.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function storeAddress(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:120'],
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:40'],
            'is_default' => ['boolean'],
        ]);

        $district = District::findOrFail($validated['district_id']);
        $thana = Thana::where('id', $validated['thana_id'])->where('district_id', $district->id)->firstOrFail();
        $area = Area::where('id', $validated['area_id'])->where('thana_id', $thana->id)->firstOrFail();

        $isDefault = (bool) ($validated['is_default'] ?? false) || $user->addresses()->count() === 0;
        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'company' => $validated['company'] ?? null,
            'country' => 'Bangladesh',
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'area_id' => $area->id,
            'district_name' => $district->name,
            'thana_name' => $thana->name,
            'area_name' => $area->name,
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'postal_code' => $validated['postal_code'] ?: $area->postal_code,
            'phone' => $validated['phone'] ?? null,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved.',
            'address' => $address->toArray(),
        ], 201);
    }

    public function updateAddress(Request $request, Address $address): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        abort_unless($address->user_id === $user->id, 403);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:120'],
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:40'],
            'is_default' => ['boolean'],
        ]);

        $district = District::findOrFail($validated['district_id']);
        $thana = Thana::where('id', $validated['thana_id'])->where('district_id', $district->id)->firstOrFail();
        $area = Area::where('id', $validated['area_id'])->where('thana_id', $thana->id)->firstOrFail();

        if (!empty($validated['is_default'])) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'company' => $validated['company'] ?? null,
            'country' => 'Bangladesh',
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'area_id' => $area->id,
            'district_name' => $district->name,
            'thana_name' => $thana->name,
            'area_name' => $area->name,
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'postal_code' => $validated['postal_code'] ?: $area->postal_code,
            'phone' => $validated['phone'] ?? null,
            'is_default' => (bool) ($validated['is_default'] ?? $address->is_default),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address updated.',
            'address' => $address->fresh()->toArray(),
        ]);
    }

    public function setDefaultAddress(Address $address): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        abort_unless($address->user_id === $user->id, 403);

        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated.',
        ]);
    }

    public function destroyAddress(Address $address): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        abort_unless($address->user_id === $user->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $user->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Address removed.',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'customer' => $user->fresh()->toArray(),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    public function destroyAccount(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        Auth::guard('web')->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('home'),
            ]);
        }

        return redirect()->route('home');
    }
}
