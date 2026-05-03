<!-- Addresses Tab -->
<div x-show="activeTab === 'addresses'" x-cloak class="space-y-6" 
     x-data="{ 
        addresses: {{ json_encode($addresses->load('district')) }},
        isAddressModalOpen: false, 
        editMode: false, 
        isSubmitting: false,
        addressForm: { id: '', type: '', name: '', phone: '', address: '', district_id: '', is_default: false },
        openAddModal() {
            this.editMode = false;
            this.addressForm = { id: '', type: '', name: '', phone: '', address: '', district_id: '', is_default: false };
            this.isAddressModalOpen = true;
        },
        openEditModal(address) {
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
            this.isAddressModalOpen = true;
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
                    this.isAddressModalOpen = false;
                    // Optional: show a success toast here
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
     
    <div class="bg-white border border-gray-200 rounded-lg p-6 md:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Addresses</h2>
            <template x-if="addresses.length < 3">
                <button @click="openAddModal" class="bg-slate-900 hover:bg-black text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition-colors">Add New</button>
            </template>
            <template x-if="addresses.length >= 3">
                <span class="text-xs text-gray-500 uppercase tracking-wider">Maximum 3 addresses reached</span>
            </template>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template x-for="address in addresses" :key="address.id">
                <div class="border border-gray-200 rounded-lg p-5 relative">
                    <template x-if="address.is_default">
                        <span class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg rounded-tr-lg uppercase tracking-wider">Default</span>
                    </template>
                    <h4 class="font-bold text-gray-900 mb-2">
                        <span x-text="address.name"></span> 
                        <template x-if="address.type"><span x-text="'(' + address.type + ')'"></span></template>
                    </h4>
                    <address class="text-sm text-gray-600 not-italic leading-relaxed mb-4">
                        <span x-text="address.address"></span><br>
                        <template x-if="address.district">
                            <span><span x-text="address.district.name"></span><br></span>
                        </template>
                        Phone: <span x-text="address.phone"></span>
                    </address>
                    <div class="flex items-center gap-3">
                        <button @click="openEditModal(address)" class="text-xs font-bold text-slate-800 hover:text-red-600 uppercase tracking-wider">Edit</button>
                        <button @click="deleteAddress(address.id)" class="text-xs font-bold text-gray-400 hover:text-red-600 uppercase tracking-wider">Delete</button>
                    </div>
                </div>
            </template>

            <template x-if="addresses.length === 0">
                <div class="col-span-full py-8 text-center text-gray-500 text-sm">
                    No addresses found. Click "Add New" to save an address.
                </div>
            </template>
        </div>
    </div>

    <!-- Address Modal -->
    <div x-show="isAddressModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div x-show="isAddressModalOpen" style="display: none;" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isAddressModalOpen = false"></div>

        <!-- Modal panel -->
        <div x-show="isAddressModalOpen" style="display: none;" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full sm:p-6">
            <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none" @click="isAddressModalOpen = false">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 uppercase tracking-widest mb-4" id="modal-title" x-text="editMode ? 'Edit Address' : 'Add New Address'"></h3>
                    <div class="mt-2">
                        <form @submit.prevent="submitAddress" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" x-model="addressForm.name" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-900 focus:border-slate-900 sm:text-sm">
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="text" x-model="addressForm.phone" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-900 focus:border-slate-900 sm:text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                                    <select x-model="addressForm.type" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-900 focus:border-slate-900 sm:text-sm">
                                        <option value="">Select Type</option>
                                        <option value="Home">Home</option>
                                        <option value="Office">Office</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                    <select x-model="addressForm.district_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-900 focus:border-slate-900 sm:text-sm">
                                        <option value="">Select District</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Address</label>
                                <textarea x-model="addressForm.address" rows="3" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-slate-900 focus:border-slate-900 sm:text-sm"></textarea>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" x-model="addressForm.is_default" class="focus:ring-slate-900 h-4 w-4 text-slate-900 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">Set as default address</label>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-slate-900 text-base font-bold uppercase tracking-wider text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 sm:ml-3 sm:w-auto sm:text-xs disabled:opacity-50">
                                    <span x-text="isSubmitting ? 'Saving...' : 'Save Address'"></span>
                                </button>
                                <button type="button" @click="isAddressModalOpen = false" :disabled="isSubmitting" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 sm:mt-0 sm:w-auto sm:text-xs uppercase tracking-wider font-bold disabled:opacity-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
