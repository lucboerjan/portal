<x-filament-widgets::widget>
    <x-filament::section heading="Weersvoorspelling — {{ $city }}">
        @if($days->count())
            <div class="grid grid-cols-5 gap-4">
                @foreach($days as $day)
                    <div class="flex flex-col items-center text-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                        <div class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                            {{ $day['date'] }}
                        </div>
                        <img 
                            src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                            class="w-12 h-12"
                            alt="{{ $day['desc'] }}"
                        >
                        <div class="text-xs text-gray-500 dark:text-gray-400 capitalize mb-1">
                            {{ $day['desc'] }}
                        </div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $day['max'] }}°
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $day['min'] }}°
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">Weersdata niet beschikbaar. Controleer je API key.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>