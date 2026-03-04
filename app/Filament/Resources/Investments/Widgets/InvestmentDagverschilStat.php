<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvestmentDagverschilStat extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        return $fondsen->map(fn ($fonds) => $this->getDagverschilStat($fonds))->toArray();
    }

    protected function getDagverschilStat(InvestmentFund $fonds): Stat
    {
        $aantalAandelen = $fonds->InvestmentPurchase()->sum('aantal');

        $koersen = $fonds->InvestmentRate()
            ->orderBy('datum', 'desc')
            ->limit(2)
            ->get();

        if ($koersen->count() < 2) {
            return Stat::make($fonds->naam . ' – Dagverschil', 'N/B')
                ->description('Onvoldoende koersdata')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray');
        }

        $huidigeKoers = $koersen->first();
        $vorigeKoers  = $koersen->last();

        $huidigeWaarde = $aantalAandelen * $huidigeKoers->dagkoers;
        $vorigeWaarde  = $aantalAandelen * $vorigeKoers->dagkoers;

        $verschilEuro = $huidigeWaarde - $vorigeWaarde;
        $verschilPct  = $vorigeWaarde != 0
            ? ($verschilEuro / $vorigeWaarde) * 100
            : 0;

        $positief  = $verschilEuro >= 0;
        $prefix    = $positief ? '+' : '';
        $vanDatum  = $vorigeKoers->datum->format('d-m-Y');
        $totDatum  = $huidigeKoers->datum->format('d-m-Y');
        $datumLabel = $vanDatum === $totDatum ? $totDatum : "{$vanDatum} → {$totDatum}";

        return Stat::make(
            $fonds->naam . ' – Dagverschil',
            $prefix . '€ ' . number_format($verschilEuro, 2, ',', '.')
        )
            ->description($prefix . number_format($verschilPct, 2, ',', '.') . '% | ' . $datumLabel)
            ->descriptionIcon($positief ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($positief ? 'success' : 'danger');
    }
}