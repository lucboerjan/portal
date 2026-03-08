<?php

namespace App\Filament\Resources\Finances\FinBeneficiaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinBeneficiariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rekeningnummer')
                    ->label('Rekeningnummer')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Gekopieerd!')
                    ->fontFamily('mono')
                    ->placeholder('—'),

                TextColumn::make('transacties_count')
                    ->label('# Transacties')
                    ->counts('transacties')
                    ->sortable(),
            ])
            ->defaultSort('naam')
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