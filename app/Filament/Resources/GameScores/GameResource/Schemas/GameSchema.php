<?php

namespace App\Filament\Resources\GameScores\GameResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class GameSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Spel details')
                    ->components([
                        Grid::make(2)->schema([
                            Select::make('game_type_id')
                                ->label('Speltype')
                                ->relationship('gameType', 'name', fn ($query) => $query->where('active', true))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),

                            DatePicker::make('played_at')
                                ->label('Datum')
                                ->required()
                                ->default(now())
                                ->displayFormat('d-m-Y')
                                ->columnSpan(1),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active'   => 'Actief',
                                    'finished' => 'Afgelopen',
                                ])
                                ->default('active')
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('description')
                                ->label('Omschrijving')
                                ->maxLength(255)
                                ->columnSpan(2),
                        ]),
                    ]),

                Section::make('Spelers')
                    ->description('Voeg minimaal 2 spelers toe.')
                    ->components([
                        Repeater::make('players')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Naam')
                                    ->required()
                                    ->maxLength(100),
                            ])
                            ->minItems(2)
                            ->maxItems(10)
                            ->addActionLabel('Speler toevoegen')
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
            ]);
    }
}