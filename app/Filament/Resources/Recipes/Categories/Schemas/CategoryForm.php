<?php

namespace App\Filament\Resources\Recipes\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;


class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return  $schema->components([
            TextInput::make('name')
                ->label('Naam')
                ->required()
                ->maxLength(255),

            TextInput::make('icon')
                ->label('Heroicon naam')
                ->placeholder('heroicon-o-fire')
                ->maxLength(255),

            ColorPicker::make('color')
                ->label('Kleur'),
        ]);
    }
}