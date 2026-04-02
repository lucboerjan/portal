<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Huidig weer --}}
        @if (isset($current['temp']))
            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">

                {{-- Bovenste rij: stad + datum --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ $city }}</div>
                    <div class="text-xs text-gray-400">{{ now()->format('d/m/Y H:i') }}</div>
                </div>

                {{-- Onderste rij: icoon, temp, details --}}
                <div class="flex items-center w-full">

                    {{-- Linkerblok --}}
                    <div class="flex items-center gap-4">
                        <img src="https://openweathermap.org/img/wn/{{ $current['weather'][0]['icon'] }}@2x.png"
                            class="w-16 h-16 shrink-0" alt="{{ $current['weather'][0]['description'] }}">

                        <div style="padding-right: 15px">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ round($current['temp']) }}°C
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                {{ $current['weather'][0]['description'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Middenblok (divider) --}}
                    <div class="flex justify-center flex-1">
                        <div class="w-px h-10 bg-gray-200 dark:bg-gray-700"></div>
                    </div>

                    {{-- Rechterblok --}}
                    <div class="flex items-center gap-8 text-sm">

                        <div style="text-align: center; padding-right: 15px;">
                            <div class="text-xs text-gray-400 mb-0.5">Voelt als</div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">
                                {{ round($current['feels_like']) }}°C
                            </div>
                        </div>

                        <div style="text-align: center; padding-right: 15px;">
                            <div class="text-xs text-gray-400 mb-0.5">Vochtigheid</div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">
                                {{ $current['humidity'] }}%
                            </div>
                        </div>

                        <div style="text-align: center; ">
                            <div class="text-xs text-gray-400 mb-0.5">Wind</div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">
                                {{ round($current['wind_speed'] * 3.6) }} km/u {{ $wind_richting }}
                            </div>
                        </div>

                    </div>

                </div>







            </div>
        @endif

        {{-- 7-daagse forecast --}}
        <div class="grid grid-cols-7 gap-3">
            @foreach ($days as $day)
                <div
                    class="flex flex-col items-center text-center px-2 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 gap-1">
                    <div class="font-semibold text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        {{ $day['date'] }}
                    </div>
                    <img src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png" class="w-10 h-10"
                        alt="{{ $day['desc'] }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize leading-tight">
                        {{ $day['desc'] }}&nbsp;&nbsp;&nbsp;
                    </div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ $day['max'] }}°C&nbsp;&nbsp;&nbsp;</div>
                    <div class="text-xs text-gray-400">{{ $day['min'] }}°C&nbsp;&nbsp;&nbsp;</div>
                    <div class="text-xs text-blue-400">🌧 {{ $day['rain'] }}%</div>
                    <div class="text-xs text-gray-400">🌬 {{ $day['wind_speed'] }} km/u {{ $day['wind_richting'] }}</div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
