<?php

namespace App\Filament\Resources\Finances\FinRekenings\Tables;

use App\Enums\RekeningType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinRekeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('omschrijving')
                    ->label('Rekening')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('referentie')
                    ->label('Referentie')
                    ->searchable()
                    ->copyable()        // handig voor rekeningnummers
                    ->copyMessage('Gekopieerd!')
                    ->fontFamily('mono'),

                TextColumn::make('rekening_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        RekeningType::Zichtrekening         => 'info',
                        RekeningType::Spaarrekening         => 'success',
                        RekeningType::Maaltijdcheques       => 'warning',
                        RekeningType::Beleggingsrekening    => 'primary',
                        RekeningType::Pensioenspaarrekening => 'danger',
                    }),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd(),

                IconColumn::make('actief')
                    ->label('Actief')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('rekening_type')
                    ->label('Type')
                    ->options(
                        collect(RekeningType::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}