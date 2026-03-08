<?php

namespace App\Filament\Resources\Finances\FinTransactions\Schemas;

use App\Models\FinBegunstigde;
use App\Models\FinCategorie;
use App\Models\FinRekening;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Transactie')->components([

                Select::make('rekening_id')
                    ->label('Rekening')
                    ->options(
                        FinRekening::where('actief', true)
                            ->orderBy('order')
                            ->pluck('omschrijving', 'id')
                    )
                    ->required()
                    ->searchable(),

                Select::make('begunstigde_id')
                    ->label('Begunstigde')
                    ->options(
                        FinBegunstigde::orderBy('naam')->pluck('naam', 'id')
                    )
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('naam')->required(),
                        TextInput::make('rekeningnummer')->nullable(),
                    ]),

                DatePicker::make('datum')
                    ->label('Datum')
                    ->required()
                    ->default(now()),

                TextInput::make('volgnummer')
                    ->label('Volgnummer')
                    ->numeric()
                    ->default(0),

                TextInput::make('omschrijving')
                    ->label('Omschrijving')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('bedrag')
                    ->label('Bedrag')
                    ->numeric()
                    ->prefix('€')
                    ->helperText('Negatief voor uitgave, positief voor inkomst')
                    ->required(),

                TextInput::make('saldo_na')
                    ->label('Saldo na transactie')
                    ->numeric()
                    ->prefix('€')
                    ->nullable(),

                Toggle::make('verwerkt')
                    ->label('Verwerkt')
                    ->default(false),

            ])->columns(2),

            Section::make('Categorisering')->components([

                Repeater::make('categorieKoppelingen')
                    ->label('Categorieën')
                    ->relationship()
                    ->schema([
                        Select::make('categorie_id')
                            ->label('Categorie')
                            ->options(function () {
                                return FinCategorie::whereNull('parent_id')
                                    ->with('children')
                                    ->get()
                                    ->mapWithKeys(fn($parent) => [
                                        $parent->omschrijving => $parent->children
                                            ->pluck('omschrijving', 'id')
                                    ]);
                            })
                            ->required()
                            ->searchable(),

                        TextInput::make('bedrag')
                            ->label('Bedrag')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Leeg = volledig bedrag'),

                        TextInput::make('opmerking')
                            ->label('Opmerking')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->addActionLabel('+ Categorie toevoegen')
                    ->defaultItems(1),

            ]),
        ]);
    }
}