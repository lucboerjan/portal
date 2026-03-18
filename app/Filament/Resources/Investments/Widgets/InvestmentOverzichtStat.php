<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;
class InvestmentOverzichtStat extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        $totaalHuidigeWaarde = 0;
        $totaalAankoopwaarde = 0;

        foreach ($fondsen as $fonds) {
            $totaalAankoopwaarde += $fonds->getTotaleInvestering();
            $totaalHuidigeWaarde += $fonds->huidige_waarde;
        }

        $totaalRendement = $totaalHuidigeWaarde - $totaalAankoopwaarde;
        $totaalRendementPercentage = $totaalAankoopwaarde > 0
            ? ($totaalRendement / $totaalAankoopwaarde) * 100
            : 0;

        return [
            Stat::make('Totale Huidige Waarde', '€ ' . number_format($totaalHuidigeWaarde, 2, ',', '.'))
                ->description('Huidige waarde van alle fondsen')
                ->descriptionIcon(Heroicon::Banknotes)
                ->color('primary'),

            Stat::make('Totale Aankoopwaarde', '€ ' . number_format($totaalAankoopwaarde, 2, ',', '.'))
                ->description('Totaal geïnvesteerd bedrag')
                ->descriptionIcon(Heroicon::ArrowTrendingDown)
                ->color('gray'),

            Stat::make('Totaal Rendement', '€ ' . number_format($totaalRendement, 2, ',', '.'))
                ->description(number_format($totaalRendementPercentage, 2, ',', '.') . '%')
                ->descriptionIcon($totaalRendement >= 0 ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown  )
                ->color($totaalRendement >= 0 ? 'success' : 'danger'),
        ];
    }

    protected function getPortfolioTrendData(): array
    {
        $data = [];
        for ($i = 600; $i >= 0; $i--) {
            $datum     = now()->subDays($i);
            $dagWaarde = 0;

            $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

            foreach ($fondsen as $fonds) {
                $dagkoers = $fonds->InvestmentRate()
                    ->whereDate('datum', '<=', $datum)
                    ->orderBy('datum', 'desc')
                    ->first();

                if ($dagkoers) {
                    $dagWaarde += $fonds->getTotalQuantityAttribute() * $dagkoers->dagkoers;
                }
            }
            $data[] = round($dagWaarde, 2);
        }
        return $data;
    }

    protected function getRendementTrendData(): array
    {
        $data    = [];
        $fondsen = InvestmentFund::all();

        $totaalAankoopwaarde = 0;
        foreach ($fondsen as $fonds) {
            $aankopen = $fonds->InvestmentPurchase;
            foreach ($aankopen as $aankoop) {
                $totaalAankoopwaarde += $aankoop->number_of_shares * $aankoop->aankoopprijs;
            }
        }

        for ($i = 6000; $i >= 0; $i -= 10) {
            $datum     = now()->subDays($i);
            $dagWaarde = 0;

            foreach ($fondsen as $fonds) {
                $laatsteKoers = $fonds->InvestmentRate()
                    ->where('datum', '<=', $datum)
                    ->orderBy('datum', 'desc')
                    ->first();

                if ($laatsteKoers) {
                    $totaalAandelen = $fonds->InvestmentPurchase()->sum('aantal');
                    $dagWaarde     += $totaalAandelen * $laatsteKoers->dagkoers;
                }
            }

            $rendement = $dagWaarde - $totaalAankoopwaarde;
            $data[]    = round($rendement, 2);
        }

        return $data;
    }
}