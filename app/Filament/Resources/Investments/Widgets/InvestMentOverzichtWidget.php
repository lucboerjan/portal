<?php

namespace App\Filament\Resources\Investments\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\InvestmentFund;

class InvestMentOverzichtWidget extends StatsOverviewWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $fondsen = InvestmentFund::with(['InvestmentRate', 'InvestmentPurchase'])->get();

        $stats = [];

        /*
        |--------------------------------------------------------------------------
        | DAGVERSCHIL
        |--------------------------------------------------------------------------
        */

        $totaalDag = $fondsen->sum(fn($f) => $f->dagverschil);

        $stats[] = Stat::make(
            'Totaal Dagverschil',
            '€ ' . number_format($totaalDag, 2, ',', '.')
        )
        ->description('Vandaag vs gisteren')
        ->color($totaalDag >= 0 ? 'success' : 'danger')
        ->icon($totaalDag >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
        ->extraAttributes(['class' => 'col-span-4']);

        foreach ($fondsen as $fonds) {
            $stats[] = Stat::make(
                $fonds->naam . ' – Dagverschil',
                '€ ' . number_format($fonds->dagverschil, 2, ',', '.')
            )
            ->description(
                number_format($fonds->dagverschil_percentage, 2, ',', '.') .
                '% | ' . $fonds->dag_start . ' → ' . $fonds->dag_eind
            )
            ->color($fonds->dagverschil >= 0 ? 'success' : 'danger')
            ->icon($fonds->dagverschil >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
            ->extraAttributes(['class' => 'col-span-1']);
        }

        /*
        |--------------------------------------------------------------------------
        | MAANDVERSCHIL
        |--------------------------------------------------------------------------
        */

        $totaalMaand = $fondsen->sum(fn($f) => $f->maandverschil);

        $stats[] = Stat::make(
            'Totaal Maandverschil',
            '€ ' . number_format($totaalMaand, 2, ',', '.')
        )
        ->description('Deze maand')
        ->color($totaalMaand >= 0 ? 'success' : 'danger')
        ->icon($totaalMaand >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
        ->extraAttributes(['class' => 'col-span-4']);

        foreach ($fondsen as $fonds) {
            $stats[] = Stat::make(
                $fonds->naam . ' – Maandverschil',
                '€ ' . number_format($fonds->maandverschil, 2, ',', '.')
            )
            ->description(
                number_format($fonds->maandverschil_percentage, 2, ',', '.') .
                '% | ' . $fonds->maand_start . ' → ' . $fonds->maand_eind
            )
            ->color($fonds->maandverschil >= 0 ? 'success' : 'danger')
            ->icon($fonds->maandverschil >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
            ->extraAttributes(['class' => 'col-span-1']);
        }

        /*
        |--------------------------------------------------------------------------
        | JAARVERSCHIL
        |--------------------------------------------------------------------------
        */

        $totaalJaar = $fondsen->sum(fn($f) => $f->jaarverschil);

        $stats[] = Stat::make(
            'Totaal Jaarverschil',
            '€ ' . number_format($totaalJaar, 2, ',', '.')
        )
        ->description('Dit jaar')
        ->color($totaalJaar >= 0 ? 'success' : 'danger')
        ->icon($totaalJaar >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
        ->extraAttributes(['class' => 'col-span-4']);

        foreach ($fondsen as $fonds) {
            $stats[] = Stat::make(
                $fonds->naam . ' – Jaarverschil',
                '€ ' . number_format($fonds->jaarverschil, 2, ',', '.')
            )
            ->description(
                number_format($fonds->jaarverschil_percentage, 2, ',', '.') .
                '% | ' . $fonds->jaar_start . ' → ' . $fonds->jaar_eind
            )
            ->color($fonds->jaarverschil >= 0 ? 'success' : 'danger')
            ->icon($fonds->jaarverschil >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
            ->extraAttributes(['class' => 'col-span-1']);
        }

        return $stats;
    }
}