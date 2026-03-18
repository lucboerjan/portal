<?php

namespace App\Filament\Pages\Finances;

use App\Livewire\Finances\FinStatsOverview;
use App\Livewire\Finances\FinMaandGrafiek;
use App\Livewire\Finances\FinRekeningenOverview;
use App\Livewire\Finances\FinControleWidget;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class FinDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Financiën Dashboard';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.finances.fin-dashboard';

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public function getWidgets(): array
    {
        return [
            FinStatsOverview::class,
            FinControleWidget::class,
            FinMaandGrafiek::class,
            FinRekeningenOverview::class,

        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}