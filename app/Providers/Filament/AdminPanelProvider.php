<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Utilities\UtilityReadings\UtilityReadingResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Illuminate\Contracts\View\View;
use Filament\View\PanelsRenderHook;
use Filament\Navigation\NavigationGroup;
use AchyutN\FilamentLogViewer\FilamentLogViewer;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        $color = env('FILAMENT_COLOR', 'gray');

        $colors = [
            'gray' => Color::Gray,
            'slate' => Color::Slate,
            'zinc' => Color::Zinc,
            'neutral' => Color::Neutral,
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
        ];

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->passwordReset()
            //->authGuard('web')
            ->colors([
                'primary' => $colors[$color] ?? Color::Violet,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,


            ])
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.collapse-nav')
            )
            ->assets([
                Js::make('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'),
                Css::make('privacy-mode', asset('css/filament/privacy-mode.css')),
                Js::make('privacy-mode', asset('js/filament/privacy-mode.js')),
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn(): View => view('filament.components.privacy-toggle'),
            )
            ->plugins([
                FilamentLogViewer::make()
                    ->navigationGroup('Systeembeheer')
                    ->navigationSort(10001), // hoog getal = onderaan,
            ])

            ->navigationGroups([
                NavigationGroup::make('Beleggingen')
                    ->label('Beleggingen')
                    ->collapsed(), // Collapses this group by default

                NavigationGroup::make('Financiën')
                    ->label('Financiën')
                    ->collapsed(), // Collapses this group by default

                NavigationGroup::make('Utilities')
                    ->label('Utilities')
                    ->collapsed(), // Collapses this group by default

                NavigationGroup::make('TV Gids')
                    ->label('TV Gids')
                    ->collapsed(), // Collapses this group by default

                NavigationGroup::make('Game Scores')
                    ->label('Game Scores')
                    ->collapsed(), // Collapses this group by default

                NavigationGroup::make('Systeembeheer')
                    ->label('Systeembeheer')
                    ->collapsed(), // Collapses this group by default

            ]);
    }

    /* public function boot()
    {
        Filament::registerRenderHook(
            'panels::body.end',
            fn() => view('filament.collapse-nav')
        );

        FilamentAsset::register([
            Js::make('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'),
        ]);
    } */
}
