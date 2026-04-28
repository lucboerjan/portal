<?php

namespace App\Livewire\Finances;

use App\Models\FinTransactie;
use Carbon\Constants\Format;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class FinStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        Carbon::setLocale('nl');
        $jaar = now()->year;
        $month = now()->month;
        $datum = Carbon::now();

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
        $maandSaldo = $inkomstenMaand + $uitgavenMaand;    

        return [
            Stat::make('Inkomsten ' . $jaar, '€ ' . number_format($inkomsten, 2, ',', '.'))
                ->description('Deze maand: € ' . number_format($inkomstenMaand, 2, ',', '.'))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make('Uitgaven ' . $jaar, '€ ' . number_format(abs($uitgaven), 2, ',', '.'))
                ->description('Deze maand: € ' . number_format(abs($uitgavenMaand), 2, ',', '.'))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make('Netto saldo ' . $jaar, '€ ' . number_format($saldo, 2, ',', '.'))
                ->description($saldo >= 0 ? 'Positief saldo' : 'Negatief saldo')
                ->color($saldo >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-scale')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make('Netto saldo ' . $datum->translatedFormat('F Y'), '€ ' . number_format($maandSaldo, 2, ',', '.'))
                ->description($maandSaldo >= 0 ? 'Positief saldo' : 'Negatief saldo')
                ->color($maandSaldo >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-scale')
                ->extraAttributes(['class' => 'privacy-sensitive']),
        ];
    }
}
