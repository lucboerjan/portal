<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class InvestmentMaandverschilStat extends BaseWidget
{
    protected static ?int $sort = 3;
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestmentFund> $fondsen */
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        $stats =  $fondsen->map(fn(InvestmentFund $fonds) => $this->getMaandverschilStat($fonds))->toArray();

        // Bereken totaal maandverschil
        $totaal = $fondsen->sum(fn($fonds) => $fonds->Maandverschil);

        $stats[] = Stat::make(
            'Totaal maandverschil',
            '€ ' . number_format($totaal, 2, ',', '.')
        )->extraAttributes(['class' => 'privacy-exempt']);

        return $stats;
    }

    protected function getMaandverschilStat(InvestmentFund $fonds): Stat
    {
        $aantalAandelen = $fonds->InvestmentPurchase()->sum('aantal');

        $huidigeKoers = $fonds->InvestmentRate()
            ->orderBy('datum', 'desc')
            ->first();

        if (! $huidigeKoers) {
            return Stat::make($fonds->naam . ' – Maandverschil', 'N/B')
                ->description('Geen koersdata beschikbaar')
                ->descriptionIcon(Heroicon::Clock)
                ->color('gray');
        }

        $eindeVorigeMaand = $huidigeKoers->datum->copy()->startOfMonth()->subDay();

        $vorigeKoers = $fonds->InvestmentRate()
            ->whereDate('datum', '<=', $eindeVorigeMaand)
            ->orderBy('datum', 'desc')
            ->first();

        if (! $vorigeKoers) {
            return Stat::make($fonds->naam . ' – Maandverschil', 'N/B')
                ->description('Geen koers vorige maand')
                ->descriptionIcon(Heroicon::Clock)
                ->color('gray');
        }

        $huidigeWaarde = $aantalAandelen * $huidigeKoers->dagkoers;
        $vorigeWaarde  = $aantalAandelen * $vorigeKoers->dagkoers;

        $verschilEuro = $huidigeWaarde - $vorigeWaarde;
        $verschilPct  = $vorigeWaarde != 0
            ? ($verschilEuro / $vorigeWaarde) * 100
            : 0;

        $positief = $verschilEuro >= 0;
        $prefix   = $positief ? '+' : '';
        $vanDatum = $vorigeKoers->datum->format('d-m-Y');
        $totDatum = $huidigeKoers->datum->format('d-m-Y');

        return Stat::make(
            $fonds->naam . ' – Maandverschil',
            $prefix . '€ ' . number_format($verschilEuro, 2, ',', '.')
        )
            ->description($prefix . number_format($verschilPct, 2, ',', '.') . '% | ' . $vanDatum . ' → ' . $totDatum)
            ->descriptionIcon($positief ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
            ->color($positief ? 'success' : 'danger')
            ->extraAttributes(['class' => 'privacy-exempt']);
    }
}
