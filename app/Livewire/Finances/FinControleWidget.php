<?php

namespace App\Livewire\Finances;

use App\Models\FinRekening;
use App\Models\FinRekeningStand;
use App\Models\FinTransactie;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;

class FinControleWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $vorigeMaand = \Carbon\Carbon::create(now()->year, now()->month, 1);
    Log::info($vorigeMaand);
        $eindeVorigeMaand = FinRekeningStand::where('jaar', $vorigeMaand->year)
            ->where('maand', $vorigeMaand->month)
            ->sum('saldo');

        $transactiesNormaal = FinTransactie::whereYear('datum', now()->year)
            ->whereMonth('datum', now()->month)
            ->whereHas('categorieen', fn($q) => $q->where('exclude', false))
            ->sum('bedrag');

        $transactiesTransfer = FinTransactie::whereYear('datum', now()->year)
            ->whereMonth('datum', now()->month)
            ->whereHas('categorieen', fn($q) => $q->where('exclude', true))
            ->sum('bedrag');

        $actueleStand = FinRekening::where('actief', true)->sum('saldo');

        $verwacht    = $eindeVorigeMaand + $transactiesNormaal;
        $verschil1   = $actueleStand - $verwacht;
        $controle1Ok = abs($verschil1) < 0.01;
        $controle2Ok = abs($transactiesTransfer) < 0.01;

        return [
            Stat::make(
                'Stand einde ' . $vorigeMaand->format('m/Y'),
                '€ ' . number_format($eindeVorigeMaand, 2, ',', '.')
            )
                ->description('Cumulatief saldo alle rekeningen')
                ->color('gray')
                ->icon('heroicon-o-archive-box')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make(
                'Transacties ' . now()->format('m/Y'),
                '€ ' . number_format($transactiesNormaal, 2, ',', '.')
            )
                ->description('Excl. transfers eigen rekeningen')
                ->color($transactiesNormaal >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-arrows-right-left')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make(
                'Verwacht saldo',
                '€ ' . number_format($verwacht, 2, ',', '.')
            )
                ->description('Stand + transacties')
                ->color('gray')
                ->icon('heroicon-o-calculator')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make(
                'Actueel saldo',
                '€ ' . number_format($actueleStand, 2, ',', '.')
            )
                ->description($controle1Ok ? '✅ Klopt met verwacht saldo' : '⚠️ Wijkt af van verwacht saldo')
                ->color($controle1Ok ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make(
                'Verschil normale transacties',
                '€ ' . number_format($verschil1, 2, ',', '.')
            )
                ->description($controle1Ok ? 'Geen afwijking' : 'Afwijking gedetecteerd!')
                ->color($controle1Ok ? 'success' : 'danger')
                ->icon($controle1Ok ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->extraAttributes(['class' => 'privacy-sensitive']),

            Stat::make(
                'Transfers eigen rekeningen',
                '€ ' . number_format($transactiesTransfer, 2, ',', '.')
            )
                ->description($controle2Ok ? 'Alle transfers zijn volledig' : '⚠️ Ontbrekende tegenboeking!')
                ->color($controle2Ok ? 'success' : 'danger')
                ->icon($controle2Ok ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                ->extraAttributes(['class' => 'privacy-sensitive']),
        ];
    }
}
