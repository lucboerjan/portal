<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;

use Filament\Actions\DeleteBulkAction;


class ImdbRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titel')
                    ->label('Titel van de film')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jaar')
                    ->label('Jaar uitgebracht')
                    ->sortable(),
                TextColumn::make('imdbrating')
                    ->label('IMDB Rating')
                    ->sortable(),
                                    TextColumn::make('imdburl')
                    ->label('IMDB URL')
                    
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->button()->color('info'),
                    DeleteAction::make()->button()->color('warning'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
