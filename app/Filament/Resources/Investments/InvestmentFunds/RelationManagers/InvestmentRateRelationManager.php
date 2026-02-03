<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Log;

class InvestmentRateRelationManager extends RelationManager
{
    protected static string $relationship = 'InvestmentRate';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Hidden::make('totaal_aantal')
                ->default(function ($livewire) {
                    return $livewire->ownerRecord
                        ->aandelenAankopen()
                        ->sum('aantal');
                })
                ->afterStateHydrated(function ($state, Set $set, $livewire) {
                    // Bij edit: herbereken het totaal aantal
                    $totaal = $livewire->ownerRecord
                        ->aandelenAankopen()
                        ->sum('aantal');
                    
                    Log::info('Totaal aantal bij hydration: ' . $totaal);
                    $set('totaal_aantal', $totaal);
                }),

            DatePicker::make('datum')
                ->required()
                ->default(function () {
                    $last = \App\Models\InvestmentRate::where('fondsID', $this->ownerRecord->id)
                        ->orderBy('datum', 'desc')
                        ->first();

                    return $last
                        ? \Carbon\Carbon::parse($last->datum)->addDay()
                        : now();
                })
                ->displayFormat('d-m-Y')
                ->native(false),

            TextInput::make('dagkoers')
                ->label('Dagkoers')
                ->numeric()
                ->required()
                ->statePath('dagkoers')
                ->live()
                ->debounce(400)
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    Log::info('Dagkoers bijgewerkt: ' . $state);
                    
                    if ($get('__updating_waarde')) {
                        Log::info('Skip dagkoers update - waarde wordt bijgewerkt');
                        return;
                    }

                    $totaal = (float) $get('totaal_aantal');
                    Log::info('Totaal aantal bij dagkoers update: ' . $totaal);
                    
                    if ($state && $totaal > 0) {
                        $dagkoers = (float) $state;
                        $nieuweWaarde = round($dagkoers * $totaal, 2);
                        
                        Log::info('Bereken waarde: ' . $dagkoers . ' * ' . $totaal . ' = ' . $nieuweWaarde);
                        
                        $set('__updating_dagkoers', true);
                        $set('waarde', $nieuweWaarde);
                        $set('__updating_dagkoers', false);
                    }
                }),

            TextInput::make('waarde')
                ->label('Totale Waarde')
                ->numeric()
                ->required()
                ->statePath('waarde')
                ->live()
                ->debounce(400)
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    Log::info('Waarde bijgewerkt: ' . $state);
                    
                    if ($get('__updating_dagkoers')) {
                        Log::info('Skip waarde update - dagkoers wordt bijgewerkt');
                        return;
                    }

                    $totaal = (float) $get('totaal_aantal');
                    Log::info('Totaal aantal bij waarde update: ' . $totaal);
                    
                    if ($state && $totaal > 0) {
                        $waarde = (float) $state;
                        $nieuweDagkoers = round($waarde / $totaal, 2);
                        
                        Log::info('Bereken dagkoers: ' . $waarde . ' / ' . $totaal . ' = ' . $nieuweDagkoers);
                        
                        $set('__updating_waarde', true);
                        $set('dagkoers', $nieuweDagkoers);
                        $set('__updating_waarde', false);
                    }
                }),

            Hidden::make('__updating_dagkoers')
                ->default(false),
                
            Hidden::make('__updating_waarde')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('datum')
            ->columns([
                TextColumn::make('datum')
                    ->date('d-m-Y')
                    ->searchable(),
                TextColumn::make('dagkoers')
                    ->label('Dagkoers')
                    ->money('EUR', true)
                    ->searchable(),
            ])
            ->defaultSort('datum', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}