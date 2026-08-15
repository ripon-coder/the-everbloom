<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class CustomerController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status']);
        $customers = $this->userRepository->getAllCustomers($filters);
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer.
     */
    public function show(int $id): View
    {
        $customer = User::with(['orders' => function($q) {
            $q->latest()->limit(10);
        }])->findOrFail($id);
        
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Update the status of the specified customer.
     */
    public function updateStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Customer status updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Customer deleted successfully.');
    }
}
