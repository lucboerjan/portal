<?php

namespace App\Filament\Resources\Finances\FinTransactions\Tables;

use App\Models\FinRekening;
use App\Models\FinCategorie;
use App\Models\FinTransactie;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ReplicateAction;
use App\Filament\Resources\Finances\FinTransactions\FinTransactionResource;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Support\Facades\Log;

class FinTransactionsTable
{


    public static function configure(Table $table): Table
    {

        return $table
            ->paginated([10, 20, 50, 75, 100, 200, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('datum')
                    ->label('Datum')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('volgnummer')
                    ->label('Nr')
                    ->sortable()
                    ->width(50),

                TextColumn::make('rekening.omschrijving')
                    ->label('Rekening')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('categorieen.omschrijving')
                    ->label('Categorie')
                    ->badge()
                    //->separator(',')
                    ->color(function ($state, $record) {
                        $categorie = $record->categorieen->first();
                        if (!$categorie) return 'gray';
                        return match ($categorie->richting->value) {
                            'inkomst' => 'success',
                            'uitgave' => 'danger',
                            default   => 'gray',
                        };
                    }),
                /* 
                TextColumn::make('begunstigde.naam')
                    ->label('Begunstigde')
                    ->searchable()
                    ->sortable(), */

                TextColumn::make('omschrijving')
                    ->label('Omschrijving')
                    ->searchable()
                    ->sortable()
                    ->limit(40),



                /*                 SelectColumn::make('categorie_id')
                    ->label('Categorie')
                    ->options(function () {
                        return FinCategorie::whereNotNull('parent_id')
                            ->with('parent')
                            ->get()
                            ->mapWithKeys(fn($cat) => [
                                $cat->id => $cat->parent->omschrijving . ' › ' . $cat->omschrijving
                            ]);
                    })
                    ->searchable(), */

                TextColumn::make('bedrag')
                    ->label('Bedrag')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd()
                    ->searchable()
                    ->summarize([
                        Sum::make()
                            ->label('Totaal')
                            ->money('EUR'),

                    ]),


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
            ->reorderable('volgnummer')
            ->modifyQueryUsing(
                fn($query) =>
                $query->orderBy('datum', 'desc')
                    ->orderBy('volgnummer', 'asc')
            )


            ->defaultSort(
                fn(Builder $query) =>
                $query->orderBy('datum', 'desc')
                    ->orderBy('volgnummer', 'asc')
            )

            ->filters([
                SelectFilter::make('rekening_id')
                    ->label('Rekening')
                    ->options(
                        FinRekening::where('actief', true)
                            ->orderBy('order')
                            ->pluck('omschrijving', 'id')
                    ),

                Filter::make('datum')
                    ->label('Datum')
                    ->schema([
                        DatePicker::make('datum')
                            ->label('Datum')
                            ->maxDate(now())
                            ->native(false),   // mooie Filament datepicker ipv browser native
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['datum'],
                                fn($q) =>
                                $q->whereDate('datum', $data['datum'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['datum']) {
                            return ['Datum: ' . \Carbon\Carbon::parse($data['datum'])->format('d/m/Y')];
                        }
                        return [];
                    }),


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

                Filter::make('exclude_categorieen')
                    ->label('Uitgesloten categorieën verbergen')
                    ->query(
                        fn(Builder $query) =>
                        $query->whereHas('categorieen', function ($q) {
                            $q->where('exclude', false);
                        })
                    )
                    ->toggle(),

                SelectFilter::make('hoofdcategorie')
                    ->label('Hoofdcategorie')
                    ->options(
                        FinCategorie::whereNull('parent_id')
                            ->orderBy('omschrijving')
                            ->pluck('omschrijving', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;

                        /* return $query->whereHas('categorieen', function ($q) use ($data) {
                            $q->where('fin_categorie.id', $data['value']);*/
                        return $query->whereHas('categorieen', function ($q) use ($data) {
                            $q->where('fin_categorie.parent_id', $data['value'])
                                ->orWhere('fin_categorie.id', $data['value']);
                        });
                    }),
                SelectFilter::make('rekeningtype')
                    ->label('Inkomst of uitgave')
                    ->options([
                        'inkomsten' => 'Inkomsten',
                        'uitgaven'  => 'Uitgaven',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;

                        return $query->whereHas('categorieen', function ($q) use ($data) {
                            $q->where('richting', match ($data['value']) {
                                'inkomsten' => 'inkomst',
                                'uitgaven'  => 'uitgave',
                            });
                        });
                    }),

            ])
            ->recordActions([
                ActionGroup::make([
                    ReplicateAction::make()
                        ->beforeReplicaSaved(function ($replica) {
                            $nieuwsteDatum = FinTransactie::where('rekening_id', $replica->rekening_id)
                                ->max('datum');

                            $replica->datum    = $nieuwsteDatum ?? now();
                            $replica->verwerkt = false;
                        })
                        ->after(function ($replica, $record) {
                            // Categorieën kopiëren van origineel naar kloon
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
                            FinTransactionResource::getUrl('edit', ['record' => $replica])
                        ),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('wijs_categorie_toe')
                        ->visible(true)
                        ->label('Categorie toewijzen')
                        ->icon(Heroicon::Tag)
                        ->schema([
                            /* Select::make('categorie_id')
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
                                ->searchable(),
 */
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
                                    // Richting overnemen van hoofdcategorie
                                    $parent = FinCategorie::find($data['parent_id']);

                                    $categorie = FinCategorie::create([
                                        'parent_id'   => $data['parent_id'],
                                        'omschrijving' => $data['omschrijving'],
                                        'richting'    => $parent->richting,
                                        'actief'      => true,
                                    ]);

                                    return $categorie->id;
                                }),
                            Select::make('actie')
                                ->label('Wat doen met bestaande categorieën?')
                                ->options([
                                    'toevoegen'  => 'Toevoegen aan bestaande',
                                    'vervangen'  => 'Bestaande vervangen',
                                ])
                                ->default('vervangen')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($data['actie'] === 'vervangen') {
                                    // Bestaande categorieën verwijderen
                                    $record->categorieKoppelingen()->delete();
                                }

                                // Controleer of categorie al gekoppeld is
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
                ]),
            ]);
    }
}
