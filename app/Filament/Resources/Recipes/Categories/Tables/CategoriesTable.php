<?php

namespace App\Filament\Resources\Recipes\Categories\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')->label('Kleur'),
                TextColumn::make('name')->label('Naam')->searchable()->sortable(),
                TextColumn::make('recipes_count')
                    ->label('Recepten')
                    ->counts('recipes')
                    ->sortable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}