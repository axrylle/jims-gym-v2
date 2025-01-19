<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-8">
        @foreach ($memberships as $membership)
            <x-filament::section>
                <div class="rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                    <!-- Main Content -->
                    <div class="relative flex flex-col md:flex-row items-center">
                        <!-- Image Section -->
                        <div class="relative w-full rounded-md md:w-1/3">
                            <img 
                                src="{{ $membership->image_name ? asset('storage/' . $membership->image_name) : asset('images/default.jpg') }}" 
                                alt="{{ $membership->name }}" 
                                class="w-full h-auto object-cover rounded-md"
                            />
                            <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
                        </div>
                        
                        <!-- Text Content Section -->
                        <div class="w-full md:w-2/3 p-6 text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $membership->name }}</h3>
                            <p class="text-gray-700 font-medium mb-4">{{ $membership->duration }} days only</p>
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Price: ₱{{ number_format($membership->price, 2) }}</h4>
                        </div>
                    </div>

                    <!-- Bottom Dropdown Section -->
                    <div x-data="{ open: false }" class="border-t border-gray-200 dark:border-gray-700">
                        <button 
                            @click="open = !open" 
                            @click.away="open = false"
                            type="button"
                            class="w-full px-6 py-3 text-left flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                        >
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Learn More</span>
                            <svg 
                                class="w-5 h-5 transform transition-transform" 
                                :class="{ 'rotate-180': open }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div 
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-800"
                        >
                            <div class="text-gray-600 dark:text-gray-300">
                                {!! $membership->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    @push('scripts')
    <script>
        console.log('Membership widget script loaded');
        
        if (typeof Alpine === 'undefined') {
            console.error('Alpine.js is not loaded. Please ensure Alpine.js is included in your page.');
        }
    </script>
    @endpush
</x-filament-widgets::widget>