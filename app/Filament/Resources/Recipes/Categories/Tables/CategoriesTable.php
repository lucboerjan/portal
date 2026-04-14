<?php

namespace App\Filament\Resources\Recipes\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}