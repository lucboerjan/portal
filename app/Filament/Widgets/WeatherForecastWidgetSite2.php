<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherForecastWidgetSite2 extends Widget
{
    protected string $view = 'filament.widgets.weather-forecast-widget-site2';
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 512;

    public function getViewData(): array
    {
        $data_site2 = Cache::remember('weather_forecast_site2', now()->addMinutes(30), function () {
            return Http::get('https://api.openweathermap.org/data/2.5/forecast', [
                'q'     => config('weather.city_2'),
                'appid' => config('weather.api_key'),
                'units' => 'metric',
                'lang'  => 'nl',
                'cnt'   => 40,
            ])->json();
        });

        $days = collect($data_site2['list'] ?? [])
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

            Log::info('WeatherForecastWidgetSite2 data', ['data' => $data_site2, 'days' => $days]);
        return ['days' => $days, 'city' => config('weather.city_2')];
    }
}
