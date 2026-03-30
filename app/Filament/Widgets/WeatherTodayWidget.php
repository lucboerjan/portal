<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherTodayWidget extends Widget
{
    protected string $view = 'filament.widgets.weather-today-widget';
    protected int|string|array $columnSpan = 1;
    protected static ?int $sort = 501;

    public function getViewData(): array
    {
        $data = Cache::remember('weather_today', now()->addMinutes(30), function () {
            return Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q'     => config('weather.city'),
                'appid' => config('weather.api_key'),
                'units' => 'metric',
                'lang'  => 'nl',
            ])->json();
        });

        return ['weather' => $data];
    }
}