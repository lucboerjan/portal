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

                TextColumn::make('meter_stand')->label('Reading Value')->sortable()->searchable()
                                    ->tooltip(function ($record) {
                        $previous = \App\Models\UtilityReading::query()
                            ->where('utility_type_id', $record->utility_type_id)
                            ->where('reading_date', '<', $record->reading_date)
                            ->orderBy('reading_date', 'desc')
                            ->value('meter_stand');

                        if ($previous === null) {
                            return 'Geen vorige meterstand beschikbaar';
                        }

                        $diff = $record->meter_stand - $previous;
                        $sign = $diff >= 0 ? '+' : '';

                        return "Verschil met vorige maand: {$sign}" . number_format($diff, 2, ',', '.') . " {$record->utilityType->unit}";
                    }),
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
