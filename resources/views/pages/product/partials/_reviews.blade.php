<div class="border-t border-gray-100 pt-4 md:pt-4" 
     x-data="{ 
        rating: 0, 
        hoverRating: 0,
        reviewText: '',
        isSubmitting: false,
        localMessage: '',
        localType: '',
        async submitReview() {
            this.localMessage = '';
            if (this.rating === 0) {
                this.localMessage = 'Please select a rating';
                this.localType = 'error';
                return;
            }
            if (!this.reviewText.trim()) {
                this.localMessage = 'Please write a review';
                this.localType = 'error';
                return;
            }

            this.isSubmitting = true;

            try {
                const response = await axios.post('{{ route('product.review.store') }}', {
                    product_id: {{ $product->id }},
                    rating: this.rating,
                    review: this.reviewText
                });

                if (response.data.success) {
                    this.localMessage = response.data.message;
                    this.localType = 'success';
                    this.rating = 0;
                    this.reviewText = '';
                }
            } catch (error) {
                this.localMessage = error.response?.data?.message || 'Something went wrong. Please try again.';
                this.localType = 'error';
            } finally {
                this.isSubmitting = false;
            }
        }
     }">
    <div class="bg-gray-50 p-6 md:p-8 rounded-md border border-gray-100 mb-10">
        <!-- Local Notification Area (Top of card) -->
        <div x-show="localMessage" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mb-6 p-4 rounded-xl flex items-center gap-3 border shadow-sm"
             :class="{
                'bg-emerald-50 border-emerald-100 text-emerald-800': localType === 'success',
                'bg-red-50 border-red-100 text-red-800': localType === 'error'
             }">
            <div class="flex-shrink-0">
                <template x-if="localType === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="localType === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-wider" x-text="localMessage"></p>
            <button @click="localMessage = ''" class="ml-auto text-current opacity-50 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Write a Review</h3>

        @auth
            <form @submit.prevent="submitReview()" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Rating <span class="text-red-600">*</span></label>
                        <div class="flex gap-1 py-2">
                            @for($i=1; $i<=5; $i++)
                                <button type="button" 
                                    @click="rating = {{ $i }}" 
                                    @mouseenter="hoverRating = {{ $i }}" 
                                    @mouseleave="hoverRating = 0"
                                    class="transition-colors focus:outline-none disabled:cursor-not-allowed"
                                    :disabled="isSubmitting"
                                    :class="(hoverRating || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Review <span class="text-red-600">*</span></label>
                    <textarea x-model="reviewText" rows="3" class="w-full border-gray-200 rounded-md text-sm focus:border-red-600 focus:ring-0 disabled:bg-gray-50 disabled:text-gray-400" placeholder="What did you think?" :disabled="isSubmitting" required></textarea>
                </div>
                <button type="submit" 
                    :disabled="rating === 0 || isSubmitting" 
                    class="bg-gray-900 hover:bg-black disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-md text-[10px] uppercase transition-colors flex items-center gap-2">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Submitting...' : 'Submit Review'">Submit Review</span>
                </button>
            </form>
        @else
            <div class="text-center py-4 bg-white rounded-md border border-dashed border-gray-200">
                <p class="text-sm text-gray-600 mb-4">You must be logged in to post a review.</p>
                <a href="{{ route('login') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-md text-[10px] uppercase transition-colors">
                    Login to Review
                </a>
            </div>
        @endauth
    </div>

    <!-- Bottom: Review List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">
                Customer Reviews ({{ $product->reviews->count() }})
            </h2>
            @if($product->reviews->count() > 0)
                <div class="flex items-center gap-1.5">
                    <div class="flex text-amber-400">
                        @php $avgRating = $product->reviews->avg('rating'); @endphp
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-3 h-3 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                    <span class="text-[11px] font-bold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                </div>
            @endif
        </div>
        
        <div class="divide-y divide-gray-50">
            @forelse($product->reviews as $review)
                <div class="py-6 first:pt-0">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5 border border-gray-200 uppercase">
                            {{ substr($review->user->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-900">{{ $review->user->name }}</span>
                                <span class="text-[10px] text-gray-300">•</span>
                                <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex text-amber-400 mb-2">
                                @for($i=1; $i<=5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $review->review }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-50/50 rounded-md border border-dashed border-gray-100">
                    <svg class="w-8 h-8 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p class="text-sm text-gray-400">No reviews yet. Be the first to review this product!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
