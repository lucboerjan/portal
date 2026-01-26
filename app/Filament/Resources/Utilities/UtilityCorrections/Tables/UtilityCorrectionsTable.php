<?php

namespace App\Filament\Resources\Utilities\UtilityCorrections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UtilityCorrectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('utilityType.name')->label('Utility Type Name')->sortable()->searchable(),
                TextColumn::make('utilityType.type')->label('Type')->sortable()->searchable(),  
                TextColumn::make('correction_date')
                    ->label('Correction Date')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('old_meter_final_reading')->label('Oude waarde')->sortable()->searchable(),
                TextColumn::make('new_meter_start_reading')->label('Nieuwe waarde')->sortable()->searchable(),
                TextColumn::make('utilityType.unit')->label('Unit')->sortable()->searchable(),
                
            ])
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
