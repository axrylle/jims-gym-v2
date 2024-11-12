<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($memberships as $membership)
            <x-filament::section>
                <div class="shadow rounded-lg p-6 text-center">
                    <h3 class="text-xl font-bold mb-2">{{ $membership->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $membership->description }}</p>
                    <p class="text-gray-700 mb-4">{{ $membership->duration }} days only</p>
                    <h3 class="text-lg text-gray-800 py-2">Price:₱ {{ number_format($membership->price, 2) }}</h3>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-widgets::widget>
