<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherTodayWidgetSite2 extends Widget
{
    protected string $view = 'filament.widgets.weather-today-widget-site2';
    protected int|string|array $columnSpan = 1;
    protected static ?int $sort = 502;

    public function getViewData(): array
    {
        $data_site2 = Cache::remember('weather_today_site2', now()->addMinutes(30), function () {
            return Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q'     => config('weather.city_2'),
                'appid' => config('weather.api_key'),
                'units' => 'metric',
                'lang'  => 'nl',
            ])->json();
        });
        Log::info(config('weather.city_2'));
Log::info('WeatherTodayWidgetSite2 data', ['data' => $data_site2]);
        return ['weather' => $data_site2];
    }
}