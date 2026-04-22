<?php

namespace App\Filament\Resources\Recipes\Recipes\Schemas;

use App\Models\Recipes\Ingredient;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return  $schema->components([

            // ── Basisinfo ───────────────────────────────────────────────────
            Section::make('Basisinformatie')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    TextInput::make('title')
                        ->label('Titel')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('category_id')
                        ->label('Categorie')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')->label('Naam')->required(),
                        ]),

                    Select::make('cooking_method_id')
                        ->label('Kookmethode')
                        ->relationship('cookingMethod', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->createOptionForm([
                            TextInput::make('name')->label('Naam')->required(),
                        ]),

                    Textarea::make('description')
                        ->label('Korte omschrijving')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Tijden & porties ────────────────────────────────────────────
            Section::make('Tijden & porties')
                ->icon('heroicon-o-clock')
                ->schema([
                    TextInput::make('prep_time')
                        ->label('Voorbereidingstijd')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('min'),

                    TextInput::make('cook_time')
                        ->label('Bereidingstijd')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('min'),

                    TextInput::make('servings')
                        ->label('Porties')
                        ->numeric()
                        ->minValue(1)
                        ->default(4)
                        ->suffix('pers.'),

                    Select::make('difficulty')
                        ->label('Moeilijkheidsgraad')
                        ->options([
                            'easy'   => 'Makkelijk',
                            'medium' => 'Gemiddeld',
                            'hard'   => 'Moeilijk',
                        ])
                        ->default('medium')
                        ->required(),
                ])
                ->columns(4),

            // ── Ingrediënten ────────────────────────────────────────────────
            Section::make('Ingrediënten')
                ->icon('heroicon-o-list-bullet')
                ->schema([
                    Repeater::make('ingredients_data')  // gewone array, geen relatie
                        ->label('')
                        ->schema([
                            Select::make('ingredient_id')
                                ->label('Ingrediënt')
                                ->options(Ingredient::orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->required()
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
                ->defaultItems(0)
                ])
                ->columnSpanFull(),

            // ── Bereiding ───────────────────────────────────────────────────
            Section::make('Bereiding')
                ->icon('heroicon-o-document-text')
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
                ])
                ->columnSpanFull(),

            // ── Bron & afbeelding ───────────────────────────────────────────
            Section::make('Bron & afbeelding')
                ->icon('heroicon-o-photo')
                ->schema([
                    Select::make('source_type')
                        ->label('Brontype')
                        ->options([
                            'url'  => 'Website (URL)',
                            'scan' => 'Ingescand recept',
                            'boek' => 'Boek / tijdschrift',
                        ])
                        ->nullable(),

                    TextInput::make('source_value')
                        ->label('Bron (URL of omschrijving)')
                        ->maxLength(500)
                        ->url(fn($get) => $get('source_type') === 'url'),

                    FileUpload::make('image')
                        ->label('Foto')
                        ->image()
                        ->directory('recipes')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Tags & notities ─────────────────────────────────────────────
            Section::make('Tags & notities')
                ->icon('heroicon-o-tag')
                ->schema([
                    Select::make('tags')
                        ->label('Tags')
                        ->multiple()
                        ->relationship('tags', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->label('Naam')->required(),
                        ]),

                    Textarea::make('notes')
                        ->label('Notities / variaties')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getTitleField(): Select
    {
        return Select::make('title')
            /* ->relationship('customer', 'name')
            ->searchable()
            ->required()
            ->live()
            ->afterStateUpdated(function (Set $set, ?int $state) {
                $set('vehicle_id', null);

                if ($state) {
                    $vehicles = Vehicle::where('customer_id', $state)->get();
                    if ($vehicles->count() === 1) {
                        $set('vehicle_id', $vehicles->first()->id);
                    }
                }
            }) */;
    }
}
