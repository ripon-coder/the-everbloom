<!-- Addresses Section -->
<div class="space-y-6" 
     x-data="{ 
        addresses: {{ json_encode($addresses->load('district')) }},
        showForm: false, 
        editMode: false, 
        isSubmitting: false,
        addressForm: { id: '', type: '', name: '', phone: '', address: '', district_id: '', is_default: false },
        openAddForm() {
            this.editMode = false;
            this.addressForm = { id: '', type: '', name: '', phone: '', address: '', district_id: '', is_default: false };
            this.showForm = true;
            this.$nextTick(() => {
                const el = document.getElementById('address-form-section');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        },
        openEditForm(address) {
            this.editMode = true;
            this.addressForm = { 
                id: address.id, 
                type: address.type || '', 
                name: address.name, 
                phone: address.phone, 
                address: address.address, 
                district_id: address.district_id, 
                is_default: address.is_default == 1 
            };
            this.showForm = true;
            this.$nextTick(() => {
                const el = document.getElementById('address-form-section');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        },
        cancelForm() {
            this.showForm = false;
            this.editMode = false;
        },
        async submitAddress() {
            this.isSubmitting = true;
            const url = this.editMode ? '{{ url('/account/addresses') }}/' + this.addressForm.id : '{{ route('account.addresses.store') }}';
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.addressForm)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.addresses = data.addresses;
                    this.showForm = false;
                    this.editMode = false;
                } else {
                    alert(data.message || data.error || 'An error occurred.');
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred. Please try again.');
            } finally {
                this.isSubmitting = false;
            }
        },
        async deleteAddress(id) {
            if (!confirm('Are you sure you want to delete this address?')) return;
            
            try {
                const response = await fetch('{{ url('/account/addresses') }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.addresses = data.addresses;
                } else {
                    alert(data.message || data.error || 'An error occurred.');
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred. Please try again.');
            }
        }
     }">
     
    <!-- Addresses Card -->
    <div class="bg-white border border-gray-200 p-5 md:p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Addresses</h2>
            <template x-if="addresses.length < 3 && !showForm">
                <button @click="openAddForm" class="bg-slate-900 hover:bg-black text-white px-4 py-2 text-xs font-bold uppercase tracking-wider transition-colors">Add New</button>
            </template>
            <template x-if="addresses.length >= 3 && !showForm">
                <span class="text-xs text-gray-500 font-medium">Maximum 3 addresses reached</span>
            </template>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template x-for="address in addresses" :key="address.id">
                <div class="border border-gray-200 p-4 relative bg-gray-50/30">
                    <template x-if="address.is_default">
                        <span class="absolute top-0 right-0 bg-primary text-white text-[10px] font-bold px-2 py-0.5 uppercase tracking-wider">Default</span>
                    </template>
                    <h4 class="font-bold text-gray-900 text-xs mb-1.5">
                        <span x-text="address.name"></span> 
                        <template x-if="address.type"><span x-text="'(' + address.type + ')'"></span></template>
                    </h4>
                    <address class="text-xs text-gray-600 not-italic leading-relaxed mb-3">
                        <span x-text="address.address"></span><br>
                        <template x-if="address.district">
                            <span><span x-text="address.district.name"></span><br></span>
                        </template>
                        Phone: <span x-text="address.phone"></span>
                    </address>
                    <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                        <button @click="openEditForm(address)" class="text-xs font-bold text-slate-800 hover:text-primary uppercase tracking-wider">Edit</button>
                        <button @click="deleteAddress(address.id)" class="text-xs font-bold text-gray-400 hover:text-red-500 uppercase tracking-wider">Delete</button>
                    </div>
                </div>
            </template>

            <template x-if="addresses.length === 0">
                <div class="col-span-full py-8 text-center text-gray-500 text-xs">
                    No addresses found. Click "Add New" to save an address.
                </div>
            </template>
        </div>
    </div>

    <!-- Inline Add / Edit Address Form Section -->
    <div id="address-form-section" x-show="showForm" x-cloak class="bg-white border border-gray-200 p-5 md:p-6 transition-all duration-300">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest" x-text="editMode ? 'Edit Address' : 'Add New Address'"></h3>
            <button type="button" @click="cancelForm" class="text-xs text-gray-400 hover:text-gray-700 font-medium">✕ Cancel</button>
        </div>

        <form @submit.prevent="submitAddress" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="addressForm.name" required class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" x-model="addressForm.phone" required class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Address Type <span class="text-red-500">*</span></label>
                    <select x-model="addressForm.type" required class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3">
                        <option value="">Select Type</option>
                        <option value="Home">Home</option>
                        <option value="Office">Office</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">District <span class="text-red-500">*</span></label>
                    <select x-model="addressForm.district_id" required class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3">
                        <option value="">Select District</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Detailed Address <span class="text-red-500">*</span></label>
                <textarea x-model="addressForm.address" rows="3" required class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3 resize-none" placeholder="House no, street, area details..."></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_default" x-model="addressForm.is_default" class="border-gray-300 text-primary focus:ring-0 h-4 w-4">
                <label for="is_default" class="ml-2 text-xs font-medium text-gray-700">Set as default address</label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" :disabled="isSubmitting" class="bg-slate-900 hover:bg-black text-white px-6 py-2.5 text-xs font-bold uppercase tracking-wide transition-colors disabled:opacity-50">
                    <span x-text="isSubmitting ? 'Saving...' : 'Save Address'"></span>
                </button>
                <button type="button" @click="cancelForm" :disabled="isSubmitting" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 text-xs font-bold uppercase tracking-wide transition-colors disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
