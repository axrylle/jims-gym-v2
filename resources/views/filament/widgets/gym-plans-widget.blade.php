<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-8">
        @foreach ($memberships as $membership)
            <x-filament::section>
                <div class="shadow rounded-lg overflow-hidden bg-white">
                    <div class="relative flex flex-col md:flex-row items-center">
                        <!-- Image Section -->
                        <div class="relative w-full md:w-1/3">
                            <img 
                                src="{{ asset('memberships/' . strtolower(str_replace(' ', '-', $membership->name)) . '.jpg') }}" 
                                alt="{{ $membership->name }}" 
                                class="w-full h-auto object-cover"
                            />
                            <div 
                                class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-white to-transparent pointer-events-none">
                            </div>
                        </div>
                        
                        <!-- Text Content Section -->
                        <div class="w-full md:w-2/3 p-6 text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $membership->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $membership->description }}</p>
                            <p class="text-gray-700 font-medium mb-4">{{ $membership->duration }} days only</p>
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Price: ₱{{ number_format($membership->price, 2) }}</h4>
                            
                            <!-- Dropdown -->
                            <div class="relative">
                                <button 
                                    onclick="toggleDropdown('dropdown-{{ $loop->index }}')" 
                                    class="bg-gray-200 px-4 py-2 rounded-lg text-gray-800 font-semibold hover:bg-gray-300 transition"
                                >
                                    Learn More
                                </button>
                                <div 
                                    id="dropdown-{{ $loop->index }}" 
                                    class="hidden absolute bg-white border rounded-lg shadow-lg mt-2 w-48 z-10"
                                >
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">View Details</a>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">Edit Plan</a>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-sm text-red-600">Delete Plan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    <!-- Dropdown Toggle Script -->
    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }
        document.addEventListener('click', function (event) {
            const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
            dropdowns.forEach((dropdown) => {
                if (!dropdown.contains(event.target) && !dropdown.previousElementSibling.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>
</x-filament-widgets::widget>
