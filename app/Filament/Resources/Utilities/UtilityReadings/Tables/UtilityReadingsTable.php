<?php

namespace App\Filament\Resources\Utilities\UtilityReadings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UtilityReadingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reading_date')
                    ->label('Reading Date')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('meter_stand')->label('Reading Value')->sortable()->searchable(),
                TextColumn::make('utilityType.name')->label('Utility Type Name')->sortable()->searchable(),
                TextColumn::make('utilityType.unit')->label('Unit')->sortable()->searchable(),
                TextColumn::make('utilityType.type')->label('Type')->sortable()->searchable(),
                //TextColumn::make('note')->label('Opmerking')->sortable()->searchable(),


            ])
            ->defaultSort('reading_date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
