<div class="border-t border-gray-100 pt-6 md:pt-8" 
     x-data="productReviewComponent({{ $product->id }}, '{{ route('product.review.store') }}')">

    @php
        $reviewCount = $product->reviews->count();
        $avgRating = $reviewCount > 0 ? $product->reviews->avg('rating') : 0;
        $ratingCounts = [];
        for ($r = 5; $r >= 1; $r--) {
            $ratingCounts[$r] = $product->reviews->where('rating', $r)->count();
        }
    @endphp

    <!-- Rating Summary + Write Review Side by Side -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Left: Rating Summary Overview -->
        <div class="bg-gray-50 border border-gray-100 p-5 md:p-6">
            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-4">Customer Ratings</h3>

            @if($reviewCount > 0)
                <div class="flex items-start gap-5">
                    <!-- Big Average Score -->
                    <div class="text-center flex-shrink-0">
                        <div class="text-4xl font-black text-gray-900 leading-none">{{ number_format($avgRating, 1) }}</div>
                        <div class="flex text-amber-400 mt-1.5 justify-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                        <div class="text-[10px] text-gray-400 mt-1 font-medium">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                    </div>

                    <!-- Rating Breakdown Bars -->
                    <div class="flex-1 space-y-1.5">
                        @for($r = 5; $r >= 1; $r--)
                            @php $pct = $reviewCount > 0 ? ($ratingCounts[$r] / $reviewCount) * 100 : 0; @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-gray-500 w-3 text-right">{{ $r }}</span>
                                <svg class="w-3 h-3 text-amber-400 fill-current flex-shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <div class="flex-1 h-2 bg-gray-200 overflow-hidden">
                                    <div class="h-full bg-amber-400 transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium w-5 text-right">{{ $ratingCounts[$r] }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-6 text-gray-400">
                    <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    <p class="text-xs font-medium">No ratings yet</p>
                </div>
            @endif
        </div>

        <!-- Right: Write Review Form -->
        <div class="bg-gray-50 border border-gray-100 p-5 md:p-6">
            <!-- Notification -->
            <div x-show="localMessage" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-4 p-3 flex items-center gap-2 border text-[11px] font-bold uppercase tracking-wider"
                 :class="{
                    'bg-emerald-50 border-emerald-200 text-emerald-700': localType === 'success',
                    'bg-red-50 border-red-200 text-red-600': localType === 'error'
                 }">
                <template x-if="localType === 'success'">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="localType === 'error'">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                <span x-text="localMessage" class="flex-1"></span>
                <button @click="localMessage = ''" class="text-current opacity-50 hover:opacity-100 flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-4">Write a Review</h3>

            @auth
                <form @submit.prevent="submitReview()" class="space-y-3">
                    <!-- Star Rating -->
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Rating <span class="text-danger">*</span></label>
                        <div class="flex gap-0.5 py-1">
                            @for($i=1; $i<=5; $i++)
                                <button type="button" 
                                    @click="rating = {{ $i }}" 
                                    @mouseenter="hoverRating = {{ $i }}" 
                                    @mouseleave="hoverRating = 0"
                                    class="transition-all duration-150 focus:outline-none disabled:cursor-not-allowed p-0.5 hover:scale-110"
                                    :disabled="isSubmitting"
                                    :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-200'">
                                    <svg class="w-7 h-7 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </button>
                            @endfor
                            <span class="ml-2 text-[11px] font-bold text-gray-400 self-center" x-show="rating > 0" x-text="['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][rating]"></span>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Review <span class="text-danger">*</span></label>
                        <textarea x-model="reviewText" rows="3" class="w-full border border-gray-200 text-sm focus:border-primary focus:ring-0 disabled:bg-gray-100 disabled:text-gray-400 resize-none p-3" placeholder="Share your experience with this product..." :disabled="isSubmitting" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        :disabled="rating === 0 || isSubmitting" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 text-[11px] uppercase tracking-wide transition-colors flex items-center justify-center gap-2 shadow-xs">
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
                <div class="text-center py-6 bg-white border border-dashed border-gray-200">
                    <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <p class="text-[11px] text-gray-500 mb-3">Login to share your review</p>
                    <a href="{{ route('login') }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-8 text-[10px] uppercase tracking-wide transition-colors">
                        Login to Review
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Review List -->
    <div>
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-0">
            <h2 class="text-xs font-bold text-gray-900 uppercase tracking-widest">
                All Reviews ({{ $reviewCount }})
            </h2>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($product->reviews as $review)
                <div class="py-5">
                    <div class="flex items-start gap-3">
                        <!-- Avatar -->
                        <div class="w-10 h-10 bg-primary/10 text-primary flex items-center justify-center text-xs font-bold flex-shrink-0 uppercase">
                            {{ substr($review->user->name ?? 'User', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <!-- Name + Date Row -->
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[12px] font-bold text-gray-900">{{ $review->user->name ?? 'Customer' }}</span>
                                    @if($review->rating >= 4)
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold px-1.5 py-0.5 uppercase">Verified</span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium flex-shrink-0">{{ $review->created_at ? $review->created_at->format('M d, Y') : '' }}</span>
                            </div>
                            <!-- Stars -->
                            <div class="flex items-center gap-1 mb-2">
                                <div class="flex text-amber-400">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                                <span class="text-[10px] font-bold text-gray-500">{{ ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$review->rating] ?? '' }}</span>
                            </div>
                            <!-- Review Text -->
                            <p class="text-[11px] text-gray-600 leading-relaxed">{{ $review->review }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p class="text-[11px] text-gray-400 font-medium">No reviews yet</p>
                    <p class="text-[11px] text-gray-300 mt-1">Be the first to review this product!</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productReviewComponent', (productId, storeUrl) => ({
            rating: 0,
            hoverRating: 0,
            reviewText: '',
            isSubmitting: false,
            localMessage: '',
            localType: '',
            async submitReview() {
                this.localMessage = '';
                if (this.rating === 0) {
                    this.localMessage = 'Please select a star rating';
                    this.localType = 'error';
                    return;
                }
                if (!this.reviewText.trim()) {
                    this.localMessage = 'Please write a review comment';
                    this.localType = 'error';
                    return;
                }

                this.isSubmitting = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            rating: this.rating,
                            review: this.reviewText
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.localMessage = data.message;
                        this.localType = 'success';
                        this.rating = 0;
                        this.reviewText = '';
                    } else {
                        this.localMessage = data.message || 'Validation error while submitting review.';
                        this.localType = 'error';
                    }
                } catch (error) {
                    this.localMessage = 'Something went wrong. Please try again.';
                    this.localType = 'error';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
