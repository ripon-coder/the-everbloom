<x-layouts.app title="Contact Us | Everbloom">
    
    <div class="bg-gray-50 py-8 md:py-12 border-b border-gray-200">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 uppercase tracking-widest mb-3 text-center">Contact Us</h1>
            <p class="text-gray-500 text-sm leading-relaxed text-center max-w-xl mx-auto">
                We're here to help! Please fill out the form below or use our contact details to get in touch with our team.
            </p>
        </div>
    </div>

    <div class="py-10 md:py-16">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-10 md:gap-16">
                
                <!-- Contact Form -->
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-widest mb-6 border-b border-gray-200 pb-2">Send a Message</h2>
                    
                    <form class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wide block mb-1">Your Name *</label>
                                <input type="text" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="Enter your name" required>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase tracking-wide block mb-1">Email Address *</label>
                                <input type="email" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide block mb-1">Subject *</label>
                            <input type="text" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="Brief subject" required>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wide block mb-1">Message *</label>
                            <textarea rows="5" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="How can we help you?" required></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-md text-sm font-bold uppercase tracking-widest transition-colors shadow-sm">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="w-full md:w-80 flex-shrink-0">
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-widest mb-6 border-b border-gray-200 pb-2">Get in Touch</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Our Store</h4>
                            <address class="text-sm text-gray-700 not-italic leading-relaxed">
                                <strong>Everbloom HQ</strong><br>
                                Level 5, Rahman Tower<br>
                                Gulshan 1, Dhaka - 1212<br>
                                Bangladesh
                            </address>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Contact Details</h4>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                Phone: <a href="tel:+8801720000000" class="text-red-600 hover:underline">+88 01720 000000</a><br>
                                Email: <a href="mailto:support@everbloom.com" class="text-red-600 hover:underline">support@everbloom.com</a>
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Business Hours</h4>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                Monday - Friday: 9am - 6pm<br>
                                Saturday: 10am - 4pm<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
