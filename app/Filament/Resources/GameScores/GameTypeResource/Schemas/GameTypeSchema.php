<?php

namespace App\Filament\Resources\GameScores\GameTypeResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class GameTypeSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Naam')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $UtilityReadingFormstate) =>
                                    $set('slug', Str::slug($state ?? ''))
                                )
                                ->columnSpan(1),

                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique()
                                ->maxLength(255)
                                ->columnSpan(1),

                            Textarea::make('description')
                                ->label('Omschrijving')
                                ->columnSpan(2),

                            Toggle::make('lowest_score_wins')
                                ->label('Laagste score wint')
                                ->default(true)
                                ->helperText('Aan = laagste score wint (Uno). Uit = hoogste score wint.')
                                ->columnSpan(1),

                            Toggle::make('active')
                                ->label('Actief')
                                ->default(true)
                                ->columnSpan(1),

                            TextInput::make('min_players')
                                ->label('Min. spelers')
                                ->numeric()
                                ->default(2)
                                ->minValue(1)
                                ->columnSpan(1),

                            TextInput::make('max_players')
                                ->label('Max. spelers')
                                ->numeric()
                                ->nullable()
                                ->minValue(2)
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}