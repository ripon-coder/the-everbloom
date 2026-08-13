@props(['endTime' => '2025-09-17T22:00:00'])

<section class="flash-sale relative py-4 sm:py-6 px-1.5 sm:px-4 bg-gray-50">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-amber-500">Flash Sale</h2>
        
        <!-- Timer Component (Alpine.js) -->
        <div x-data="flashSaleTimer('{{ $endTime }}')" class="flex gap-2 text-sm font-medium mt-2 md:mt-0">
            <template x-if="isExpired">
                <span class="text-danger font-medium mt-2 md:mt-0">Flash Sale Ended</span>
            </template>
            <template x-if="!isExpired">
                <div class="flex gap-2 text-sm font-medium mt-2 md:mt-0">
                    <span class="bg-amber-500 text-white rounded-none text-center w-10 py-1 inline-block" x-text="hours"></span>
                    <span class="bg-amber-500 text-white rounded-none text-center w-10 py-1 inline-block" x-text="minutes"></span>
                    <span class="bg-amber-500 text-white rounded-none text-center w-10 py-1 inline-block" x-text="seconds"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Big image -->
    <div class="relative w-full h-64 md:h-96 overflow-hidden shadow-lg">
        <img src="https://laz-img-cdn.alicdn.com/images/ims-web/TB1zCGTKNYaK1RjSZFnXXa80pXa.jpg" alt="Flash Sale" class="w-full h-full object-cover" />
    </div>

    <script>
        function registerFlashSaleTimer() {
            if (typeof Alpine !== 'undefined') {
                Alpine.data('flashSaleTimer', (endTime) => ({
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                    isExpired: false,
                    interval: null,
                    init() {
                        this.calculateTimeLeft();
                        this.interval = setInterval(() => {
                            this.calculateTimeLeft();
                        }, 1000);
                    },
                    calculateTimeLeft() {
                        const diff = new Date(endTime).getTime() - new Date().getTime();
                        if (diff <= 0) {
                            this.isExpired = true;
                            if(this.interval) clearInterval(this.interval);
                            return;
                        }
                        this.hours = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                        this.minutes = String(Math.floor((diff / (1000 * 60)) % 60)).padStart(2, '0');
                        this.seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');
                    }
                }));
            }
        }
        if (typeof Alpine !== 'undefined') {
            registerFlashSaleTimer();
        } else {
            document.addEventListener('alpine:init', registerFlashSaleTimer);
        }
    </script>
</section>
