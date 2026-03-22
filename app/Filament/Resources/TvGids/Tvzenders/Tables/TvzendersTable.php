<?php

namespace App\Filament\Resources\TvGids\Tvzenders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;

class TvzendersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('volgnummer')
            ->columns([
                TextColumn::make('naam')
                    ->label('Zendernaam')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('volgnummer')
                    ->label('Volgorde')
                    ->sortable(),
            ])
            ->defaultSort('volgnummer')
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->button()->color('info'),
                    DeleteAction::make()->button()->color('warning'),
                ])
            ]);
    }
}
