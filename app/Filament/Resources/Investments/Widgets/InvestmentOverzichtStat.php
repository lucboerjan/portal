<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvestmentOverzichtStat extends BaseWidget
{
    protected static ?int $sort = 1;

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
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart($this->getPortfolioTrendData()),

            Stat::make('Totale Aankoopwaarde', '€ ' . number_format($totaalAankoopwaarde, 2, ',', '.'))
                ->description('Totaal geïnvesteerd bedrag')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('gray'),

            Stat::make('Totaal Rendement', '€ ' . number_format($totaalRendement, 2, ',', '.'))
                ->description(number_format($totaalRendementPercentage, 2, ',', '.') . '%')
                ->descriptionIcon($totaalRendement >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($totaalRendement >= 0 ? 'success' : 'danger')
                ->chart($this->getRendementTrendData()),
        ];
    }

    protected function getPortfolioTrendData(): array
    {
        // Laatste 7 dagen portfolio waarde
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $datum = now()->subDays($i);
            $dagWaarde = 0;

            $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();
            foreach ($fondsen as $fonds) {
                $dagkoers = $fonds->InvestmentRate()
                    ->whereDate('datum', '<=', $datum)
                    ->orderBy('datum', 'desc')
                    ->first();

                if ($dagkoers) {
                    $dagWaarde += $fonds->totaal_aandelen * $dagkoers->koers;
                }
            }
            $data[] = round($dagWaarde, 2);
        }

        return $data;
    }


    protected function getRendementTrendData(): array
    {
        $data = [];
        $fondsen = InvestmentFund::all();

        // Bereken aankoopwaarde eenmalig
        $totaalAankoopwaarde = 0;
        foreach ($fondsen as $fonds) {
            $aankopen = $fonds->InvestmentPurchase; // Gebruikt relatie property (zonder ())
            foreach ($aankopen as $aankoop) {
                $totaalAankoopwaarde += $aankoop->number_of_shares * $aankoop->purchase_price;
            }
        }

        for ($i = 6; $i >= 0; $i--) {
            $datum = now()->subDays($i);
            $dagWaarde = 0;

            foreach ($fondsen as $fonds) {
                // Gebruik query builder met ()
                $laatsteKoers = $fonds->InvestmentRate()
                    ->where('datum', '<=', $datum)
                    ->orderBy('datum', 'desc')
                    ->first();

                if ($laatsteKoers) {
                    $totaalAandelen = $fonds->InvestmentPurchase()->sum('aantal');
                    $dagWaarde += $totaalAandelen * $laatsteKoers->investment_rate;
                }
            }

            $rendement = $dagWaarde - $totaalAankoopwaarde;
            $data[] = round($rendement, 2);
        }

        return $data;
    }
}
