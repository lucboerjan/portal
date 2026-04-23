<?php

namespace App\Filament\Resources\Recipes\Recipes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecipesTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular(),

                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->weight('FontWeight::Bold'),

                TextColumn::make('category.name')
                    ->label('Categorie')
                    ->badge()
                    ->sortable(),

                TextColumn::make('cookingMethod.name')
                    ->label('Methode')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('difficulty')
                    ->label('Niveau')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'easy'   => 'Makkelijk',
                        'medium' => 'Gemiddeld',
                        'hard'   => 'Moeilijk',
                        default  => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'easy'   => 'success',
                        'medium' => 'warning',
                        'hard'   => 'danger',
                        default  => 'gray',
                    }),

                TextColumn::make('total_time')
                    ->label('Totale tijd')
                    ->getStateUsing(fn ($record) => $record->total_time ? $record->total_time . ' min' : '—'),

                TextColumn::make('servings')
                    ->label('Porties')
                    ->suffix(' pers.')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Categorie'),

                SelectFilter::make('cooking_method')
                    ->relationship('cookingMethod', 'name')
                    ->label('Kookmethode'),

                SelectFilter::make('difficulty')
                    ->label('Niveau')
                    ->options([
                        'easy'   => 'Makkelijk',
                        'medium' => 'Gemiddeld',
                        'hard'   => 'Moeilijk',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('title');
    }
}