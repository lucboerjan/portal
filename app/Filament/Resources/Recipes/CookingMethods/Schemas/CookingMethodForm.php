<?php

namespace App\Filament\Resources\Recipes\CookingMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Filament\Support\Icons\Heroicon;

class CookingMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Naam')
                ->required()
                ->maxLength(255),

            TextInput::make('icon')
                ->label('Heroicon naam')
                ->placeholder('Heroicon::o-fire')
                ->maxLength(255),
        ]);
    }
}