@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Edit Profile</h1>
        </div>
        
        <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Mobile -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $admin->mobile) }}"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                @error('mobile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="my-6 border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" id="current_password"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm transition-colors">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
