<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Schemas;

use Dom\Text;
use App\Models\FinRekening;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

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
                    ->maxLength(20),
                TextInput::make('url')
                    ->label('Website')
                    ->suffixAction(
                        Action::make('open')
                            ->icon(Heroicon::OutlinedLink)
                            ->visible(fn($state)=>($state != ''))
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                    ),
                TextInput::make('fondsType')
                    ->label('Fonds Type')
                    ->maxLength(100),

                Select::make('rekening_id')
                    ->label('Gekoppelde rekening')
                    ->options(FinRekening::all()->pluck('omschrijving', 'id')),

                TextInput::make('berekend_saldo')
                    ->label(fn(callable $get) => 'Laatst berekend saldo - ' . $get('rekening_naam'))
                    ->prefix('€')
                    ->disabled()
                    ->dehydrated(false),

                // Verborgen veld voor de naam
                TextInput::make('rekening_naam')
                    ->hidden()
                    ->dehydrated(false),
            ]);
    }
}
