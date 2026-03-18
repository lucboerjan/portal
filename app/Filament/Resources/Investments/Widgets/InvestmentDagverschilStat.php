<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class InvestmentDagverschilStat extends BaseWidget
{
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = null;

    /*     protected function getStats(): array
    {
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        return $fondsen->map(fn ($fonds) => $this->getDagverschilStat($fonds))->toArray();
    } */


    protected function getStats(): array
    {
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        $stats = $fondsen
            ->map(fn($fonds) => $this->getDagverschilStat($fonds))
            ->toArray();

        $totaal = $fondsen->sum(fn($fonds) => $fonds->Dagverschil);

        $stats[] = Stat::make(
            'Totaal Dagverschil',
            '€ ' . number_format($totaal, 2, ',', '.')
        );

        return $stats;

        return $fondsen->map(fn($fonds) => $this->getDagverschilStat($fonds))->toArray();
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
                ->descriptionIcon(Heroicon::Clock)
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
            ->descriptionIcon($positief ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
            ->color($positief ? 'success' : 'danger');
    }
}
