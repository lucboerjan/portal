<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherForecastWidget extends Widget
{
    protected string $view = 'filament.widgets.weather-forecast-widget';
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 511;

    public function getViewData(): array
    {
        $data = Cache::remember('weather_forecast', now()->addMinutes(30), function () {
            return Http::get('https://api.openweathermap.org/data/2.5/forecast', [
                'q'     => config('weather.city'),
                'appid' => config('weather.api_key'),
                'units' => 'metric',
                'lang'  => 'nl',
                'cnt'   => 40,
            ])->json();
        });

        $days = collect($data['list'] ?? [])
            ->groupBy(fn($item) => date('Y-m-d', $item['dt']))
            ->map(fn($items, $date) => [
                'date' => date('D d/m', strtotime($date)),
                'min'  => round(collect($items)->min('main.temp_min')),
                'max'  => round(collect($items)->max('main.temp_max')),
                'icon' => $items->first()['weather'][0]['icon'],
                'desc' => $items->first()['weather'][0]['description'],
            ])
            ->take(5)
            ->values();

        return ['days' => $days, 'city' => config('weather.city')];
    }
}