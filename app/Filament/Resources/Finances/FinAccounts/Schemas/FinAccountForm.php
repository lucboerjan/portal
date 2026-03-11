<?php

namespace App\Filament\Resources\Finances\FinAccounts\Schemas;

use App\Enums\RekeningType;
use App\Models\FinTransactie;
use Filament\Schemas\Components\Text;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Rekening')->components([

                TextInput::make('referentie')
                    ->label('Rekeningnummer / Referentie')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                TextInput::make('omschrijving')
                    ->label('Omschrijving')
                    ->required()
                    ->maxLength(255),

                Select::make('rekening_type')
                    ->label('Type')
                    ->options(
                        collect(RekeningType::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                    )
                    ->required(),

                TextInput::make('order')
                    ->label('Volgorde')
                    ->numeric()
                    ->default(0),

                Toggle::make('actief')
                    ->label('Actief')
                    ->default(true),

            ])->columns(2),

            Section::make('Saldo')->components([

                TextInput::make('saldo')
                    ->label('Huidig saldo (manueel ingegeven)')
                    ->numeric()
                    ->prefix('€')
                    ->default(0),
                    //->helperText('Manueel ingegeven beginsaldo'),

                TextInput::make('berekend_saldo')
                    ->label('Berekend saldo (som transacties)')
                    ->prefix('€')
                    ->disabled()
                    ->dehydrated(false),


                TextInput::make('verschil_saldo')
                    ->label('Verschil (saldo - berekend)')
                    ->prefix('€')
                    ->disabled()
                    ->dehydrated(false),
            ])->columns(3),

        ]);
    }
}
