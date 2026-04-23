<?php

namespace App\Filament\Resources\Recipes\Recipes\Schemas;

use App\Models\Recipes\Ingredient;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Str;

class RecipeWizardSteps
{
    public static function getGeneralInfoStep(): Step
    {
        return Step::make('Basisinformatie')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Categorie')
                            ->native(false)
                            ->options(\App\Models\Recipes\Category::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('cooking_method_id')
                            ->label('Kookmethode')
                            ->native(false)
                            ->options(\App\Models\Recipes\CookingMethod::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Korte omschrijving')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getIngredientsStep(): Step
    {
        return Step::make('Ingrediënten')
            ->description('Ingrediënten en hoeveelheden')
            ->icon('heroicon-o-list-bullet')
            ->schema([
                Section::make()
                    ->schema([
                        Repeater::make('ingredients_data')  // gewone array, GEEN ->relationship()
                            ->label('')
                            ->schema([
                                Select::make('ingredient_id')
                                    ->label('Ingrediënt')
                                    ->options(Ingredient::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')->label('Naam')->required(),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return Ingredient::create([
                                            'name' => $data['name'],
                                            'slug' => Str::slug($data['name']),
                                        ])->id;
                                    })
                                    ->columnSpan(3),

                                TextInput::make('quantity')
                                    ->label('Hoeveelheid')
                                    ->numeric()
                                    ->step(0.01)
                                    ->columnSpan(1),

                                Select::make('unit')
                                    ->label('Eenheid')
                                    ->options([
                                        'g'    => 'gram (g)',
                                        'kg'   => 'kilogram (kg)',
                                        'ml'   => 'milliliter (ml)',
                                        'l'    => 'liter (l)',
                                        'tl'   => 'theelepel (tl)',
                                        'el'   => 'eetlepel (el)',
                                        'kop'  => 'kop',
                                        'stuk' => 'stuk',
                                        'snuf' => 'snufje',
                                    ])
                                    ->searchable()
                                    ->columnSpan(2),

                                TextInput::make('notes')
                                    ->label('Opmerking')
                                    ->placeholder('bijv. fijngesneden')
                                    ->columnSpan(2),
                            ])
                            ->columns(8)
                            ->reorderable()
                            ->addActionLabel('Ingrediënt toevoegen')
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public static function getBereidingStep(): Step
    {
        return Step::make('Bereiding')
            ->description('Bereidingsinstructies')
            ->icon('heroicon-o-document-text')
            ->schema([
                Section::make()
                    ->schema([
                        Repeater::make('instructions')
                            ->label('Stappen')
                            ->schema([
                                Textarea::make('step')
                                    ->label('Stap')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->reorderable()
                            ->addActionLabel('Stap toevoegen')
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}