<?php

namespace App\Filament\Resources\Utilities\UtilityTypes\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UtilityTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Utility Type Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('unit')
                    ->label('Unit of Measurement')
                    ->required()
                    ->maxLength(50),
                TextInput::make('type')
                    ->label('Type')
                    ->required()
                    ->maxLength(100),
            ]);
    }
}
