<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;

class InvestmentFundsTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('naam')->label('Naam')->sortable()->searchable(),
                TextColumn::make('isin')->label('ISIN')->sortable()->searchable(),
                //TextColumn::make('fondsType')->label('Fonds Type')->sortable()->searchable(),
                TextColumn::make('aantal')
                    ->label('Totaal Aantal')
                    ->getStateUsing(function ($record) {
                        return $record->aandelenAankopen()->sum('aantal');
                    })
                    ->numeric(),

                TextColumn::make('huidigeWaarde')
                    ->label('Huidige Waarde')
                    ->money('EUR', true),


                TextColumn::make('laatste_koers_datum')
                    ->label('Updated')
                    ->getStateUsing(function ($record) {
                        return $record->laatste_koers?->datum;
                    })
                    ->date('d-m-Y'),
                TextColumn::make('laatste_dagkoers')
                    ->label('Laatste Dagkoers')
                    ->getStateUsing(function ($record) {
                        return $record->laatste_koers?->dagkoers;
                    })
                    ->money('EUR', true),

                TextColumn::make('totale_investering')
                    ->label('Aankoopbedrag')
                    ->getStateUsing(fn($record) => $record->getTotaleInvestering())
                    ->money('EUR', true),

                TextColumn::make('rendement_euro')
                    ->label('Rendement')
                    ->state(fn($record) => $record->rendement_euro)
                    ->money('EUR', true)
                    ->description(fn($record) => number_format($record->rendement_percentage, 2) . '%')
                    ->color(fn($record) => $record->rendement_euro >= 0 ? 'success' : 'danger')
                    ->icon(fn($record) => $record->rendement_euro >= 0 ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
                    ->iconPosition('before'),

/* 
                TextColumn::make('rendement')
                    ->label('Rendement')
                    ->getStateUsing(function ($record) {
                        $totaleInvestering = $record->getTotaleInvestering();
                        $huidigeWaarde = $record->getHuidigeWaardeAttribute();
                        $rendementEuro = $huidigeWaarde - $totaleInvestering;
                        $rendementPercentage = $totaleInvestering > 0
                            ? ($rendementEuro / $totaleInvestering) * 100
                            : 0;

                        return [
                            'euro' => $rendementEuro,
                            'percentage' => $rendementPercentage
                        ];
                    })
                    ->formatStateUsing(function ($state) {
                        // Check of $state een array is
                        if (!is_array($state)) {
                            return '-';
                        }

                        $euro = number_format($state['euro'], 2, ',', '.');
                        $percentage = number_format($state['percentage'], 2, ',', '.');
                        $sign = $state['euro'] >= 0 ? '+' : '';

                        return "{$sign}€{$euro} ({$sign}{$percentage}%)";
                    })
                    ->icon(function ($state) {
                        if (!is_array($state)) return null;
                        return $state['euro'] >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
                    })
                    ->iconPosition('before')
                    ->color(function ($state) {
                        if (!is_array($state)) return 'gray';
                        return $state['euro'] >= 0 ? 'success' : 'danger';
                    })
                    ->weight('semibold')
                    ->sortable(true), */


            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('view_dagkoers')
                        ->label('Dagkoers')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->url(fn($record) => $record->url)
                        ->openUrlInNewTab()
                        ->visible(fn($record) => !empty($record->url)),
                ]),
            ]);
    }
}
