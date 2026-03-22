<?php

namespace App\Filament\Resources\Finances\FinAccounts\RelationManagers;

use App\Models\FinBegunstigde;
use App\Models\FinCategorie;
use App\Models\FinTransactie;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Facades\FilamentView;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class TransactiesRelationManager extends RelationManager
{
    protected static string $relationship = 'transacties';
    protected static ?string $title = 'Transacties';

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 20, 50, 75, 100, 200, 'all'])
            ->defaultPaginationPageOption(10)
            ->defaultSort('datum', 'desc')
            ->reorderable('volgnummer')
            ->defaultSort(
                function (Builder $query) {
                    return $query->orderBy('datum', 'desc')
                        ->orderBy('volgnummer', 'asc');
                }
            )
            ->columns([
                TextColumn::make('datum')
                    ->label('Datum')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('volgnummer')
                    ->label('Nr')
                    ->sortable()
                    ->width(50),

                TextColumn::make('begunstigde.naam')
                    ->label('Begunstigde')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('omschrijving')
                    ->label('Omschrijving')
                    ->searchable()
                    ->limit(40),
                    
                TextColumn::make('categorieen.omschrijving')
                    ->label('Categorie')
                    ->badge()
                   // ->separator(',')
                    ->color(function ($state, $record) {
                        $categorie = $record->categorieen->first();
                        if (!$categorie) return 'gray';

                        return match ($categorie->richting->value) {
                            'inkomst' => 'success',
                            'uitgave' => 'danger',
                            default   => 'gray',
                        };
                    }),

                TextColumn::make('bedrag')
                    ->label('Bedrag')
                    ->money('EUR')
                    ->sortable()
                    ->searchable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd(),

                TextColumn::make('saldo_na')
                    ->label('Saldo')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                IconColumn::make('verwerkt')
                    ->label('✓')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('datum')
                    ->label('Periode')
                    ->schema([
                        DatePicker::make('van')
                            ->label('Van')
                            ->native(false),
                        DatePicker::make('tot')
                            ->label('Tot')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['van'], fn($q) => $q->whereDate('datum', '>=', $data['van']))
                            ->when($data['tot'], fn($q) => $q->whereDate('datum', '<=', $data['tot']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['van']) {
                            $indicators[] = 'Van: ' . \Carbon\Carbon::parse($data['van'])->format('d/m/Y');
                        }
                        if ($data['tot']) {
                            $indicators[] = 'Tot: ' . \Carbon\Carbon::parse($data['tot'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                SelectFilter::make('verwerkt')
                    ->label('Verwerkt')
                    ->options([
                        '1' => 'Verwerkt',
                        '0' => 'Niet verwerkt',
                    ]),

                Filter::make('inkomsten')
                    ->label('Alleen inkomsten')
                    ->query(fn(Builder $q) => $q->where('bedrag', '>', 0)),

                Filter::make('uitgaven')
                    ->label('Alleen uitgaven')
                    ->query(fn(Builder $q) => $q->where('bedrag', '<', 0)),

                SelectFilter::make('hoofdcategorie')
                    ->label('Hoofdcategorie')
                    ->options(
                        FinCategorie::whereNull('parent_id')
                            ->orderBy('omschrijving')
                            ->pluck('omschrijving', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;
                        return $query->whereHas('categorieen', function ($q) use ($data) {
                            $q->where('fin_categorie.parent_id', $data['value'])
                                ->orWhere('fin_categorie.id', $data['value']);
                        });
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nieuwe transactie')
                    ->schema(fn() => $this->getTransactieForm())
                    ->successRedirectUrl(
                        fn() =>
                        \App\Filament\Resources\Finances\FinAccounts\FinAccountResource::getUrl('edit', [
                            'record' => $this->getOwnerRecord()->id
                        ])
                    )
                    ->after(function () {
                        $this->dispatch('recalculate-saldo');
                    }),
            ])
            ->recordActions([
                Action::make('categoriseer')
                ->visible(fn($record) => !$record->categorieen->isNotEmpty())
                    ->label('Categorie')
                    ->icon(Heroicon::Tag)
                    ->color('gray')
                    ->schema([
                        Select::make('categorie_id')
                            ->label('Categorie')
                            ->options(function () {
                                return FinCategorie::whereNotNull('parent_id')
                                    ->with('parent')
                                    ->get()
                                    ->mapWithKeys(fn($cat) => [
                                        $cat->id => $cat->parent->omschrijving . ' › ' . $cat->omschrijving
                                    ]);
                            })
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                Select::make('parent_id')
                                    ->label('Hoofdcategorie')
                                    ->options(
                                        FinCategorie::whereNull('parent_id')
                                            ->pluck('omschrijving', 'id')
                                    )
                                    ->required(),
                                TextInput::make('omschrijving')
                                    ->label('Omschrijving')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $parent = FinCategorie::find($data['parent_id']);
                                $categorie = FinCategorie::create([
                                    'parent_id'    => $data['parent_id'],
                                    'omschrijving' => $data['omschrijving'],
                                    'richting'     => $parent->richting,
                                    'actief'       => true,
                                ]);
                                return $categorie->id;
                            })
                            ->default(
                                fn($record) =>
                                $record->categorieKoppelingen()->first()?->categorie_id
                            ),
                    ])
                    ->action(function ($record, array $data) {
                        $record->categorieKoppelingen()->delete();
                        $record->categorieKoppelingen()->create([
                            'categorie_id' => $data['categorie_id'],
                            'bedrag'       => null,
                        ]);
                        $record->update(['verwerkt' => true]);
                    }),

                ReplicateAction::make()
                    ->beforeReplicaSaved(function ($replica) {
                        $nieuwsteDatum = FinTransactie::max('datum');

                        $replica->datum    = $nieuwsteDatum ?? now();
                        $replica->verwerkt = false;
                    })
                    ->after(function ($replica, $record) {
                        foreach ($record->categorieKoppelingen as $koppeling) {
                            $replica->categorieKoppelingen()->create([
                                'categorie_id' => $koppeling->categorie_id,
                                'bedrag'       => $koppeling->bedrag,
                                'opmerking'    => $koppeling->opmerking,
                            ]);
                        }
                    })
                    ->successRedirectUrl(
                        fn($replica) =>
                        \App\Filament\Resources\Finances\FinTransactions\FinTransactionResource::getUrl('edit', [
                            'record' => $replica->id
                        ])
                    ),

                EditAction::make()
                    ->schema(fn() => $this->getTransactieForm())
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        $data['categorie_id'] = $record->categorieKoppelingen()->first()?->categorie_id;
                        return $data;
                    })
                    ->using(function ($record, array $data) {
                        $categorieId = $data['categorie_id'] ?? null;
                        unset($data['categorie_id']);

                        $record->update($data);

                        $record->categorieKoppelingen()->delete();
                        if ($categorieId) {
                            $record->categorieKoppelingen()->create([
                                'categorie_id' => $categorieId,
                                'bedrag'       => null,
                            ]);

                            // Transactie als verwerkt markeren
                            $record->update(['verwerkt' => true]);
                        }

                        return $record;
                    })
                    ->after(function () {
                        $this->dispatch('recalculate-saldo');
                    }),

                DeleteAction::make()->after(function () {
                    $this->dispatch('recalculate-saldo');
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('wijs_categorie_toe')
                        ->visible(fn($records) => $records->every(fn($record) => $record->categorieKoppelingen()->isEmpty()))
                        ->label('Categorie toewijzen')
                        ->icon(Heroicon::Tag)
                        ->schema([
                            Select::make('categorie_id')
                                ->label('Categorie')
                                ->options(function () {
                                    return FinCategorie::whereNotNull('parent_id')
                                        ->with('parent')
                                        ->get()
                                        ->mapWithKeys(fn($cat) => [
                                            $cat->id => $cat->parent->omschrijving . ' › ' . $cat->omschrijving
                                        ]);
                                })
                                ->required()
                                ->searchable()
                                ->createOptionForm([
                                    Select::make('parent_id')
                                        ->label('Hoofdcategorie')
                                        ->options(
                                            FinCategorie::whereNull('parent_id')
                                                ->pluck('omschrijving', 'id')
                                        )
                                        ->required(),
                                    TextInput::make('omschrijving')
                                        ->label('Omschrijving')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->createOptionUsing(function (array $data) {
                                    $parent = FinCategorie::find($data['parent_id']);
                                    $categorie = FinCategorie::create([
                                        'parent_id'    => $data['parent_id'],
                                        'omschrijving' => $data['omschrijving'],
                                        'richting'     => $parent->richting,
                                        'actief'       => true,
                                    ]);
                                    return $categorie->id;
                                }),

                            Select::make('actie')
                                ->label('Wat doen met bestaande categorieën?')
                                ->options([
                                    'toevoegen' => 'Toevoegen aan bestaande',
                                    'vervangen' => 'Bestaande vervangen',
                                ])
                                ->default('vervangen')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($data['actie'] === 'vervangen') {
                                    $record->categorieKoppelingen()->delete();
                                }
                                $bestaatAl = $record->categorieKoppelingen()
                                    ->where('categorie_id', $data['categorie_id'])
                                    ->exists();
                                if (!$bestaatAl) {
                                    $record->categorieKoppelingen()->create([
                                        'categorie_id' => $data['categorie_id'],
                                        'bedrag'       => null,
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->after(function () {
                            $this->dispatch('recalculate-saldo');
                        }),
                ]),
            ]);
    }

    private function getTransactieForm(): array
    {
        return [
            Section::make('Transactie')->components([
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
                    ->native(false)
                    ->default(
                        fn() =>
                        FinTransactie::max('datum') ?? now()
                    ),

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
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if (!$state) return;

                        $categorieId = $get('categorie_id');
                        if (!$categorieId) return;

                        $categorie = FinCategorie::find($categorieId);
                        if (!$categorie) return;

                        // Enkel bij uitgaven automatisch negatief maken
                        // Bij inkomsten mag het negatief blijven (bvb beleggingen)
                        if ($categorie->richting->value === 'uitgave' && $state > 0) {
                            $set('bedrag', -abs($state));
                        }
                    }),

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


                Select::make('categorie_id')
                    ->label('Categorie')
                    ->options(function () {
                        return FinCategorie::whereNotNull('parent_id')
                            ->with('parent')
                            ->get()
                            ->mapWithKeys(fn($cat) => [
                                $cat->id => $cat->parent->omschrijving . ' › ' . $cat->omschrijving
                            ]);
                    })
                    ->searchable()
                    ->default(fn($record) => $record?->categorieKoppelingen()->first()?->categorie_id)
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if (!$state) return;
                        $categorie = FinCategorie::find($state);
                        $bedrag    = $get('bedrag');
                        if (!$bedrag) return;
                        if ($categorie->richting->value === 'uitgave' && $bedrag > 0) {
                            $set('bedrag', -abs($bedrag));
                        } elseif ($categorie->richting->value === 'inkomst' && $bedrag < 0) {
                            $set('bedrag', $bedrag);
                        }
                    })
                    ->createOptionForm([
                        Select::make('parent_id')
                            ->label('Hoofdcategorie')
                            ->options(
                                FinCategorie::whereNull('parent_id')->pluck('omschrijving', 'id')
                            )
                            ->required(),
                        TextInput::make('omschrijving')
                            ->label('Omschrijving')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $parent = FinCategorie::find($data['parent_id']);
                        $categorie = FinCategorie::create([
                            'parent_id'    => $data['parent_id'],
                            'omschrijving' => $data['omschrijving'],
                            'richting'     => $parent->richting,
                            'actief'       => true,
                        ]);
                        return $categorie->id;
                    }),
            ])->columns(1),
        ];
    }
}
