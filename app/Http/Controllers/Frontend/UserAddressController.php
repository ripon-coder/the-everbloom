<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->addresses()->count() >= 3) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'You can only save up to 3 addresses.'], 403);
            return back()->with('error', 'You can only save up to 3 addresses.');
        }

        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'is_default' => 'nullable|boolean',
        ]);

        $isFirst = $user->addresses()->count() === 0;
        $isDefault = $request->has('is_default') && $request->is_default || $isFirst;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'district_id' => $validated['district_id'],
            'is_default' => $isDefault,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Address added successfully.', 'addresses' => $user->addresses()->with('district')->get()]);
        }

        return back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, $id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->has('is_default') && $request->is_default;

        if ($isDefault && !$address->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'district_id' => $validated['district_id'],
            'is_default' => $isDefault || $address->is_default, // if it was default, keep it default unless another is chosen
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Address updated successfully.', 'addresses' => auth()->user()->addresses()->with('district')->get()]);
        }

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $firstAddress = auth()->user()->addresses()->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Address deleted successfully.', 'addresses' => auth()->user()->addresses()->with('district')->get()]);
        }

        return back()->with('success', 'Address deleted successfully.');
    }
}
