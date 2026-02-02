<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvestmentFundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('naam')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('isin')
                    ->label('ISIN')
                    ->required()
                    ->maxLength(12),
                TextInput::make('url')
                    ->label('Dagkoers URL')
                    ->url()
                    ->maxLength(255),
                TextInput::make('fondsType')
                    ->label('Fonds Type')
                    ->maxLength(100),
            ]);
    }
}
