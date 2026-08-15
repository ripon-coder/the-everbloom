<!-- Account Details Section -->
<div class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-none p-5 md:p-6 shadow-xs">
        <h2 class="text-sm sm:text-base font-semibold text-gray-900 uppercase tracking-wide mb-6">Account Details</h2>
        
        <form method="POST" action="{{ route('account.details.update') }}" class="space-y-5">
            @csrf
            
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-3.5 rounded-none text-xs sm:text-sm font-semibold uppercase tracking-wide mb-6 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @php
                    $names = explode(' ', $user->name, 2);
                    $firstName = $names[0] ?? '';
                    $lastName = $names[1] ?? '';
                @endphp
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $firstName) }}" class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $lastName) }}" class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div>
                <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">Mobile Number *</label>
                <input type="text" 
                       name="phone" 
                       value="{{ old('phone', $user->phone) }}" 
                       class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5 @error('phone') border-red-500 @enderror"
                       maxlength="11"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)">
                @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 mt-4 border-t border-gray-100">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Password Change</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">Current Password</label>
                        <input type="password" name="current_password" class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5 @error('current_password') border-red-500 @enderror">
                        @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">New Password</label>
                        <input type="password" name="new_password" class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5 @error('new_password') border-red-500 @enderror">
                        @error('new_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wide block mb-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="w-full border-gray-300 rounded-none text-sm focus:ring-0 focus:border-emerald-600 py-3 px-3.5">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-slate-900 hover:bg-black text-white px-6 py-2.5 rounded-none text-xs font-semibold uppercase tracking-wide transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
