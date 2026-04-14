<?php

namespace App\Filament\Resources\Recipes\Ingredients\Schemas;

use Filament\Forms\Components\TextInput;

class IngredientForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Naam')
                ->required()
                ->maxLength(255),
        ];
    }
}