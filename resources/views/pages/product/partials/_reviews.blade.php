<div class="border-t border-gray-100 pt-4 md:pt-4">
    <!-- Top: Add Review Form -->
    <div class="bg-gray-50 p-6 md:p-8 rounded-md border border-gray-100 mb-10">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Write a Review</h3>
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Name</label>
                    <input type="text" class="w-full border-gray-200 rounded-md text-sm focus:border-red-600 focus:ring-0 py-2.5" placeholder="Enter your name">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Rating</label>
                    <div class="flex gap-1 text-gray-300 py-2">
                        @for($i=1; $i<=5; $i++)
                            <button type="button" class="hover:text-yellow-400 transition-colors">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Review</label>
                <textarea rows="3" class="w-full border-gray-200 rounded-md text-sm focus:border-red-600 focus:ring-0" placeholder="What did you think?"></textarea>
            </div>
            <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-3 px-8 rounded-md text-[10px] uppercase transition-colors">
                Submit Review
            </button>
        </form>
    </div>

    <!-- Bottom: Review List -->
    <div class="space-y-6">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-100 pb-4">Customer Reviews</h2>
        
        @foreach([1, 2] as $index)
            <div class="border-b border-gray-50 pb-6 last:border-0">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                        {{ $index == 1 ? 'RA' : 'TH' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-900">{{ $index == 1 ? 'Rahat Ahmed' : 'Tanvir H.' }}</span>
                            <span class="text-[10px] text-gray-300">•</span>
                            <span class="text-[10px] text-gray-400">{{ $index == 1 ? '2 days ago' : '1 week ago' }}</span>
                        </div>
                        <div class="flex text-amber-400 mb-2">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">"{{ $index == 1 ? 'The build quality is exceptional. Exactly as described.' : 'Great product, but the packaging could be better.' }}"</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>