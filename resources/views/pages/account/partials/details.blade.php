<!-- Account Details Section -->
<div class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6 md:p-8 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6">Account Details</h2>
        
        <form method="POST" action="{{ route('account.details.update') }}" class="space-y-5">
            @csrf
            
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-md text-sm font-bold uppercase tracking-wider mb-6">
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
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $firstName) }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $lastName) }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div>
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 @error('email') border-red-500 @enderror">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 mt-4 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Password Change</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Current Password</label>
                        <input type="password" name="current_password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 @error('current_password') border-red-500 @enderror">
                        @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">New Password</label>
                        <input type="password" name="new_password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 @error('new_password') border-red-500 @enderror">
                        @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
