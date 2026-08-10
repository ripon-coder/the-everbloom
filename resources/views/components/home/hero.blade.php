@props(['sliders' => collect()])

<section class="max-w-[1400px] mx-auto px-4 py-4">
    @if($sliders->isNotEmpty())
        <!-- Swiper Container -->
        <div class="swiper heroSwiper rounded-md overflow-hidden border border-gray-200 shadow-sm relative group">
            <div class="swiper-wrapper">
                @foreach($sliders as $slider)
                    <div class="swiper-slide relative">
                        <div class="relative w-full h-[150px] sm:h-[250px] md:h-[350px] lg:h-[400px] bg-gray-100">
                            <img src="{{ $slider->getImageUrl() }}" alt="{{ $slider->title }}"
                                class="w-full h-full object-cover">
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black/30 flex items-center px-8 sm:px-16 md:px-24">
                                <div class="max-w-2xl text-white space-y-3">
                                    @if($slider->subtitle)
                                        <h3 class="text-xs sm:text-sm md:text-base font-medium tracking-wide animate-fade-in-up">
                                            {{ $slider->subtitle }}
                                        </h3>
                                    @endif
                                    @if($slider->title)
                                        <h1
                                            class="text-xl sm:text-2xl md:text-4xl font-black leading-tight drop-shadow-md animate-fade-in-up delay-100">
                                            {{ $slider->title }}
                                        </h1>
                                    @endif
                                    @if($slider->btn_text && $slider->btn_link)
                                        <div class="pt-2 animate-fade-in-up delay-200">
                                            <a href="{{ $slider->btn_link }}"
                                                class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold rounded-full transition-all transform hover:scale-105 shadow-lg">
                                                {{ $slider->btn_text }}
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    @else
        <!-- Fallback Hero -->
        <div
            class="relative w-full h-[150px] sm:h-[250px] md:h-[350px] lg:h-[400px] bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl overflow-hidden flex items-center justify-center border border-gray-200 shadow-inner">
            <div class="text-center flex flex-col items-center justify-center z-20 px-4">
                <h1 class="text-xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-slate-800 mb-2 drop-shadow-sm">
                    প্রোডাক্ট বুঝুন, অর্ডার করুন
                </h1>
                <p class="text-lg sm:text-2xl md:text-3xl font-bold text-slate-600 mt-2">
                    ডেলিভারি হবে <span
                        class="text-red-600 font-black text-2xl sm:text-4xl md:text-5xl drop-shadow-md">সেইইইই</span> স্পিডে
                    সারা দেশে
                </p>
            </div>
        </div>
    @endif
</section>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .swiper-pagination-bullet-active {
            background: #ef4444 !important;
            width: 24px !important;
            border-radius: 4px !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('.heroSwiper')) {
                new Swiper('.heroSwiper', {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                });
            }
        });
    </script>
@endpush
