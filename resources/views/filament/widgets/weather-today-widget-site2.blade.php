<x-filament-widgets::widget>
    <x-filament::section>
        @if(isset($weather['main']))
            <div class="flex items-center gap-4">
                <img 
                    src="https://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png"
                    class="w-16 h-16"
                    alt="{{ $weather['weather'][0]['description'] }}"
                >
                <div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ round($weather['main']['temp']) }}°C
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                        {{ $weather['weather'][0]['description'] }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Voelt als {{ round($weather['main']['feels_like']) }}°C
                        · 💧 {{ $weather['main']['humidity'] }}%
                        · 🌬 {{ round($weather['wind']['speed'] * 3.6) }} km/u
                    </div>
                </div>
                <div class="ml-auto text-right">
                    <div class="font-semibold text-gray-700 dark:text-gray-300">
                        {{ $weather['name'] }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-400">Weersdata niet beschikbaar. Controleer je API key.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>