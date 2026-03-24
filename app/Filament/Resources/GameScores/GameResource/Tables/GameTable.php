<?php

namespace App\Filament\Resources\GameScores\GameResource\Tables;

use App\Filament\Resources\GameScores\GameResource;
use App\Models\Game;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;   

class GameTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('played_at')
                    ->label('Datum')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gameType.name')
                    ->label('Speltype')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Omschrijving')
                    ->searchable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('players_count')
                    ->label('Spelers')
                    ->counts('players')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rounds_count')
                    ->label('Rondes')
                    ->counts('rounds')
                    ->sortable(),

                // BadgeColumn bestaat niet meer in v5 → TextColumn met ->badge()
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active'   => 'warning',
                        'finished' => 'success',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'active'   => 'Actief',
                        'finished' => 'Afgelopen',
                        default    => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('game_type_id')
                    ->label('Speltype')
                    ->relationship('gameType', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active'   => 'Actief',
                        'finished' => 'Afgelopen',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('scorebord')
                        ->label('Scorebord')
                        ->icon(Heroicon::OutlinedRocketLaunch)
                        ->visible(fn(Game $record) => $record->status === 'active')
                        ->color('success')
                        ->url(fn(Game $record) => GameResource::getUrl('scorebord', ['record' => $record])),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->defaultSort('played_at', 'desc');
    }
}
