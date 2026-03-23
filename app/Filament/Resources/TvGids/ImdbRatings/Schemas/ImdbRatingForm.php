<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ImdbRatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('titel')
                ->label('Titel van de film')
                ->required()
                ->maxLength(80)
                ->columnSpan(2),
            TextInput::make('jaar')
                ->label('Jaar eerste vertoning')
                ->numeric()
                ->minValue(1900)
                ->maxValue(2099),
            TextInput::make('imdbrating')
                ->label('IMDB Rating')
                ->numeric()
                ->step(0.1)
                ->minValue(0)
                ->maxValue(10),
            TextInput::make('imdburl')
                ->label('IMDB URL')
                ->url()
                ->maxLength(80)
                ->columnSpan(2),
        ])->columns(2);
    }
}
