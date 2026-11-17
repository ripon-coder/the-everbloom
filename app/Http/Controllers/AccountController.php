<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Area;
use App\Models\District;
use App\Models\Order;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
        $loginType = $request->input('login_type');
        if (! in_array($loginType, ['phone', 'email'], true)) {
            $loginType = $request->filled('email') ? 'email' : 'phone';
        }

        if ($loginType === 'email') {
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
            ])->withInput($request->only('email', 'login_type'));
        }

        $credentials = $request->validate([
            'phone' => ['required', 'string', 'min:6', 'max:40'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->findActiveUserByPhone($credentials['phone']);

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('account'));
        }

        return back()->withErrors([
            'phone' => 'Invalid mobile number or password.',
        ])->withInput($request->only('phone', 'login_type'));
    }

    private function findActiveUserByPhone(string $inputPhone): ?User
    {
        return $this->findUserByPhone($inputPhone, activeOnly: true);
    }

    private function findUserByPhone(string $inputPhone, bool $activeOnly = true): ?User
    {
        $trimmed = trim($inputPhone);

        $exactQuery = User::query()->where('phone', $trimmed);
        if ($activeOnly) {
            $exactQuery->where('status', 'Active');
        }

        $exact = $exactQuery->first();
        if ($exact) {
            return $exact;
        }

        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';
        if ($digits === '') {
            return null;
        }

        $candidates = [$digits];
        if (str_starts_with($digits, '0') && strlen($digits) >= 10) {
            $candidates[] = '880' . substr($digits, 1);
        } elseif (str_starts_with($digits, '880') && strlen($digits) >= 12) {
            $candidates[] = '0' . substr($digits, 3);
        }

        $query = User::query()->whereNotNull('phone');
        if ($activeOnly) {
            $query->where('status', 'Active');
        }

        return $query->get()->first(function (User $user) use ($candidates) {
            $userDigits = preg_replace('/\D+/', '', (string) $user->phone) ?? '';

            return $userDigits !== '' && in_array($userDigits, $candidates, true);
        });
    }

    public function register(): View
    {
        return view('frontend.account.register');
    }

    public function registerSubmit(Request $request): RedirectResponse
    {
        $registerType = $request->input('register_type');
        if (! in_array($registerType, ['phone', 'email'], true)) {
            $registerType = $request->filled('email') ? 'email' : 'phone';
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'terms' => ['accepted'],
        ];

        if ($registerType === 'email') {
            $rules['email'] = ['required', 'string', 'email', 'max:191', 'unique:users,email'];
        } else {
            $rules['phone'] = ['required', 'string', 'min:6', 'max:40'];
        }

        $validated = $request->validate($rules);

        if ($registerType === 'phone' && $this->findUserByPhone($validated['phone'], activeOnly: false)) {
            return back()->withErrors([
                'phone' => 'This mobile number is already registered.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $registerType === 'email' ? $validated['email'] : null,
            'phone' => $registerType === 'phone' ? trim($validated['phone']) : null,
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
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        PasswordFacade::broker('users')->sendResetLink([
            'email' => $validated['email'],
        ]);

        return back()->with('status', 'If an account matches that email, a password reset link has been sent.');
    }

    public function resetPassword(Request $request, string $token): View
    {
        return view('frontend.account.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPasswordSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $status = PasswordFacade::broker('users')->reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordFacade::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => [__($status)],
        ]);
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
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address_line1' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:40'],
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
            'country' => 'Bangladesh',
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'area_id' => $area->id,
            'district_name' => $district->name,
            'thana_name' => $thana->name,
            'area_name' => $area->name,
            'address_line1' => $validated['address_line1'],
            'phone' => $validated['phone'],
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
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address_line1' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:40'],
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
            'country' => 'Bangladesh',
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'area_id' => $area->id,
            'district_name' => $district->name,
            'thana_name' => $thana->name,
            'area_name' => $area->name,
            'address_line1' => $validated['address_line1'],
            'phone' => $validated['phone'],
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
            'email' => ['nullable', 'email', 'max:191', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:40'],
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
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
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
