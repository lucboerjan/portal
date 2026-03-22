<?php

namespace App\Filament\Resources\GameScores\GameTypeResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;

class GameTypeTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('lowest_score_wins')
                    ->label('Laagste wint')
                    ->boolean(),

                Tables\Columns\TextColumn::make('min_players')
                    ->label('Min. spelers')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_players')
                    ->label('Max. spelers')
                    ->default('∞'),

                Tables\Columns\TextColumn::make('games_count')
                    ->label('Spellen')
                    ->counts('games')
                    ->sortable(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Actief')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Actief'),
            ])
            ->recordActions([
                ActionGroup::make([
                    

                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ])
            ->defaultSort('name');
    }
}
