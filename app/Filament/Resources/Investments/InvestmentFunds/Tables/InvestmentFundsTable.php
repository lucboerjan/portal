<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvestmentFundsTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('naam')->label('Name')->sortable()->searchable(),
                TextColumn::make('isin')->label('ISIN')->sortable()->searchable(),
                TextColumn::make('fondsType')->label('Fonds Type')->sortable()->searchable(),

                                TextColumn::make('huidigeWaarde')
                    ->label('Huidige Waarde')
                    ->money('EUR', true),
                    
                TextColumn::make('url')
                    ->label('Link')
                    ->formatStateUsing(fn($state) => $state ? 'Dagkoers' : '-')
                    ->url(fn($record) => $record->url)
                    ->openUrlInNewTab()
                    ->icon(fn($record) => $record->url ? 'heroicon-o-arrow-top-right-on-square' : null)
                    ->iconPosition('after')
                    ->color(fn($record) => $record->url ? 'primary' : 'gray')
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
