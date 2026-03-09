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
                    ->separator(','),

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
                    ->schema(fn() => $this->getTransactieForm()),
            ])
            ->recordActions([
                Action::make('categoriseer')
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
                        $nieuwsteDatum = FinTransactie::where('rekening_id', $replica->rekening_id)
                            ->max('datum');
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
                        \App\Filament\Resources\Finances\FinTransactions\FinTransactionResource::getUrl('edit', ['record' => $replica])
                    ),

                EditAction::make()
                    ->schema(fn() => $this->getTransactieForm()),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('wijs_categorie_toe')
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

                    DeleteBulkAction::make(),
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
                        FinTransactie::where('rekening_id', $this->getOwnerRecord()->id)
                            ->max('datum') ?? now()
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
                                return FinCategorie::whereNotNull('parent_id')
                                    ->with('parent')
                                    ->get()
                                    ->mapWithKeys(fn($parent) => [
                                        $parent->id => $parent->parent->omschrijving . ' › ' . $parent->omschrijving
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
        ];
    }
}
