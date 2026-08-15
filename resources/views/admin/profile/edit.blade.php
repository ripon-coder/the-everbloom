@extends('admin.layouts.app')

@section('title', 'Admin Profile & Security')

@section('content')
    <div class="space-y-6" x-data="{
        showCurrentPass: false,
        showNewPass: false
    }">
        <!-- Single Unified Profile Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Account</span>
                            <span class="text-gray-300">/</span>
                            <span>Admin Profile</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Profile & Security Settings</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                Administrator #{{ $admin->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Administrator Mini Badge -->
                    <div class="flex items-center space-x-3 bg-white p-2 border border-gray-200 shadow-xs">
                        <div class="w-9 h-9 bg-gray-900 text-white font-bold text-sm flex items-center justify-center">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-gray-900">{{ $admin->name }}</p>
                            <p class="text-gray-500 text-[11px]">{{ $admin->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Section 1: Basic Information -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Personal & Contact Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $admin->name) }}"
                                       class="w-full px-3 py-2 text-xs border @error('name') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('name')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" required
                                       value="{{ old('email', $admin->email) }}"
                                       class="w-full px-3 py-2 text-xs border @error('email') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('email')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mobile -->
                            <div>
                                <label for="mobile" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Mobile Phone Number
                                </label>
                                <input type="text" name="mobile" id="mobile"
                                       value="{{ old('mobile', $admin->mobile) }}"
                                       placeholder="+8801812345678"
                                       class="w-full px-3 py-2 text-xs border @error('mobile') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('mobile')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Security & Password Update -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 mb-4 border-b border-gray-200 gap-1">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">
                                Security & Password Modification
                            </h2>
                            <span class="text-[11px] text-gray-500">Leave blank if you do not wish to change your password</span>
                        </div>

                        <!-- 3 Password Fields Perfectly Aligned in 3 Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Current Password
                                </label>
                                <div class="relative">
                                    <input :type="showCurrentPass ? 'text' : 'password'" name="current_password" id="current_password"
                                           placeholder="Enter current password..."
                                           class="w-full pl-3 pr-9 py-2 text-xs border @error('current_password') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    <button type="button" @click="showCurrentPass = !showCurrentPass"
                                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                            title="Toggle password visibility">
                                        <svg x-show="!showCurrentPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="showCurrentPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    New Password
                                </label>
                                <div class="relative">
                                    <input :type="showNewPass ? 'text' : 'password'" name="password" id="password"
                                           placeholder="At least 8 characters..."
                                           class="w-full pl-3 pr-9 py-2 text-xs border @error('password') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    <button type="button" @click="showNewPass = !showNewPass"
                                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                            title="Toggle password visibility">
                                        <svg x-show="!showNewPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="showNewPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Confirm New Password
                                </label>
                                <input :type="showNewPass ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                                       placeholder="Repeat new password..."
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
                    <button type="submit"
                            class="px-6 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Update Profile
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
