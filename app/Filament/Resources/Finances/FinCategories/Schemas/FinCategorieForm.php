<?php

namespace App\Filament\Resources\Finances\FinCategories\Schemas;

use App\Enums\CategorieRichting;
use App\Models\FinCategorie;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;

class FinCategorieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('parent_id')
                ->label('Hoofdcategorie')
                ->placeholder('— Dit is een hoofdcategorie —')
                ->options(
                    FinCategorie::whereNull('parent_id')
                        ->pluck('omschrijving', 'id')
                )
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $parent = FinCategorie::find($state);
                        $set('richting', $parent?->richting?->value);
                    }
                })
                ->columnSpanFull(),

            TextInput::make('omschrijving')
                ->label('Omschrijving')
                ->required()
                ->maxLength(255),

            Select::make('richting')
                ->label('Richting')
                ->options(
                    collect(CategorieRichting::cases())
                        ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
                )
                ->required()
                ->disabled(fn(Get $get) => filled($get('parent_id'))),

            Toggle::make('exclude')
                ->label('Uitsluiten van rapporten')
                ->default(false),

            Toggle::make('actief')
                ->label('Actief')
                ->default(true),
        ]);
    }
}