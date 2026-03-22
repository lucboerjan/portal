<?php

namespace App\Filament\Resources\TvGids\Tvzenders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class TvzenderForm
{
    public static function configure(Schema $schema): Schema
    {
        
        return $schema->components([
            TextInput::make('naam')
                ->label('Zendernaam')
                ->required()
                ->maxLength(15),
            TextInput::make('volgnummer')
                ->label('Volgnummer')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }
}
