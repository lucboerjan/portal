<?php

namespace App\Filament\Resources\Finances\FinBeneficiaries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinBeneficiaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('naam')
                ->label('Naam')
                ->required()
                ->maxLength(255),

            TextInput::make('rekeningnummer')
                ->label('Rekeningnummer')
                ->nullable()
                ->maxLength(255),

        ]);
    }
}