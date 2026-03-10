<?php

namespace App\Livewire\Finances;

use App\Models\FinTransactie;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $jaar = now()->year;
        $inkomsten = FinTransactie::whereYear('datum', $jaar)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'inkomst')
                    ->where('exclude', false);
            })
            ->sum('bedrag');

        $uitgaven = FinTransactie::whereYear('datum', $jaar)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'uitgave')
                    ->where('exclude', false);
            })
            ->sum('bedrag');

        $saldo = $inkomsten + $uitgaven;

        // Huidige maand
        $inkomstenMaand = FinTransactie::whereYear('datum', $jaar)
            ->whereMonth('datum', now()->month)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'inkomst')
                    ->where('exclude', false);
            })
            ->sum('bedrag');

        $uitgavenMaand = FinTransactie::whereYear('datum', $jaar)
            ->whereMonth('datum', now()->month)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'uitgave')
                    ->where('exclude', false);
            })
            ->sum('bedrag');

        return [
            Stat::make('Inkomsten ' . $jaar, '€ ' . number_format($inkomsten, 2, ',', '.'))
                ->description('Deze maand: € ' . number_format($inkomstenMaand, 2, ',', '.'))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),

            Stat::make('Uitgaven ' . $jaar, '€ ' . number_format(abs($uitgaven), 2, ',', '.'))
                ->description('Deze maand: € ' . number_format(abs($uitgavenMaand), 2, ',', '.'))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),

            Stat::make('Netto saldo ' . $jaar, '€ ' . number_format($saldo, 2, ',', '.'))
                ->description($saldo >= 0 ? 'Positief saldo' : 'Negatief saldo')
                ->color($saldo >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-scale'),
        ];
    }
}
