<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherBredeneWidget extends Widget
{
    protected string $view = 'filament.widgets.weather-location';
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 501;
    protected static ?string $pollingInterval = '10m';

    public function getViewData(): array
    {
        $data = Cache::remember('weather_bredene', now()->addMinutes(10), function () {
            return Http::get('https://api.openweathermap.org/data/3.0/onecall', [
                'lat'     => 51.2223344,
                'lon'     => 2.9688547,
                'appid'   => config('weather.api_key'),
                'units'   => 'metric',
                'lang'    => 'nl',
                'exclude' => 'minutely,hourly,alerts',
            ])->json();
        });
        //Log::channel('weather')->info('Weather data for Bredene', ['data' => $data]);
        //dd($data);

        return $this->formatData($data, 'Bredene');
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
                'wind_speed'    => round($day['wind_speed'] * 3.6),
                'wind_richting' => $this->windRichting($day['wind_deg'] ?? 0),
            ])
            ->values();
        return  [
            'city'          => $city,
            'current'       => $current,
            'wind_richting' => $this->windRichting($current['wind_deg'] ?? 0),
            'days'          => $days,
        ];
    }

    protected function windRichting(int $deg): string
    {
        $richtingen = [
            'N',
            'NNO',
            'NO',
            'ONO',
            'O',
            'OZO',
            'ZO',
            'ZZO',
            'Z',
            'ZZW',
            'ZW',
            'WZW',
            'W',
            'WNW',
            'NW',
            'NNW',
        ];

        return $richtingen[round($deg / 22.5) % 16];
    }
}
