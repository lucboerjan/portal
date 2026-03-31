<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherManhayWidget extends Widget
{
    protected string $view = 'filament.widgets.weather-location';
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 502;
    protected static ?string $pollingInterval = '10m';

    public function getViewData(): array
    {
        $data = Cache::remember('weather_manhay', now()->addMinutes(10), function () {
            return Http::get('https://api.openweathermap.org/data/3.0/onecall', [
                'lat'     => 50.294303404998615,
                'lon'     =>  5.721819542113242,
                'appid'   => config('weather.api_key'),
                'units'   => 'metric',
                'lang'    => 'nl',
                'exclude' => 'minutely,hourly,alerts',
            ])->json();
        });

        return $this->formatData($data, 'Manhay');
    }

    protected function formatData(array $data, string $city): array
    {
        $dagen = ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'];
        $maanden = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];

        $current = $data['current'] ?? [];

        $days = collect($data['daily'] ?? [])
            ->take(7)
            ->map(fn($day) => [
                'date' => $dagen[date('w', $day['dt'])] . ' ' . date('d', $day['dt']) . ' ' . $maanden[date('n', $day['dt']) - 1],
                'min'  => round($day['temp']['min']),
                'max'  => round($day['temp']['max']),
                'icon' => $day['weather'][0]['icon'],
                'desc' => $day['weather'][0]['description'],
                'rain' => $day['pop'] ? round($day['pop'] * 100) : 0,
            ])
            ->values();

        return [
            'city'    => $city,
            'current' => $current,
            'days'    => $days,
        ];
    }
}
