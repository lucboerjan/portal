<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AppsWidget extends Widget
{
    protected  string $view = 'filament.widgets.apps-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 10;

    /**
     * Define your apps here.
     *
     * For 'icon', use a verified Heroicon outline name (without prefix).
     * Check existence at: https://heroicons.com
     *
     * Commonly safe icons:
     *   home, user, users, cog-6-tooth, chart-bar, chart-pie,
     *   document-text, folder, inbox, bell, calendar,
     *   currency-euro, shopping-cart, truck, globe-alt, link,
     *   map-pin, phone, envelope, calculator, clipboard-document,
     *   star, heart, fire, light-bulb, beaker, academic-cap,
     *   briefcase, building-office-2, arrow-trending-up,
     *   banknotes, credit-card, gift, puzzle-piece,
     *   rocket-launch, shield-check, sparkles, trophy, wrench-screwdriver
     */
    public function getApps(): array
    {
        return [
            [
                'label'  => 'Tankbeurten',
                'url'    => $this->url('tankbeurten'),
                'icon'   => 'tankbeurten',            // heroicon-o-truck
                'color'  => 'primary',          // primary|success|warning|danger|info
                'target' => '_blank',
            ],
            [
                'label'  => 'Lottobingo',
                'url'    => $this->url('lottobingo'),
                'icon'   => 'lottobingo',           // heroicon-o-ticket
                'color'  => 'primary',
                'target' => '_blank',
            ],
            [
                'label'  => 'Stamboom',
                'url'    => $this->url('stamboom'),
                'icon'   => 'stamboom',            // heroicon-o-users
                'color'  => 'primary',
                'target' => '_blank',
            ],
            [
                'label'  => 'Boskluivers',
                'url'    => $this->url('boskluivers'),
                'icon'   => 'boskluivers',            // heroicon-o-users
                'color'  => 'primary',
                'target' => '_blank',
            ],
            
            [
                'label'  => 'Contacts',
                'url'    => $this->url('cdb'),
                'icon'   => 'cdb',            // heroicon-o-users
                'color'  => 'primary',
                'target' => '_blank',
            ],
            [
                'label'  => 'HHB-Desante',
                'url'    => $this->url('hhb-desante'),
                'icon'   => 'hhb-desante',            // heroicon-o-users
                'color'  => 'primary',
                'target' => '_blank',
            ],
            
            // Add more apps here:
            // [
            //     'label'  => 'Nieuwe App',
            //     'url'    => 'https://nieuweapp.example.com',
            //     'icon'   => 'globe-alt',
            //     'color'  => 'success',
            //     'target' => '_blank',
            // ],
        ];
    }

    private function url(string $subdomain): string
{
    if (env('APP_ENV') === 'local') {
        return 'http://' . $subdomain . '.' . env('APP_DOMAIN', 'local');
    }   
    return 'https://' . $subdomain . '.' . env('APP_DOMAIN', 'local');
}
}