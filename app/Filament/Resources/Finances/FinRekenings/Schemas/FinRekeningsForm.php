<?php

namespace App\Filament\Resources\Finances\FinRekenings\Schemas;

use App\Enums\RekeningType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FinRekeningsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

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

            TextInput::make('saldo')
                ->label('Huidig saldo')
                ->numeric()
                ->prefix('€')
                ->default(0),

            TextInput::make('order')
                ->label('Volgorde')
                ->numeric()
                ->default(0),

            Toggle::make('actief')
                ->label('Actief')
                ->default(true),
        ]);
    }
}