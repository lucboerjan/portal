<?php

namespace App\Filament\Resources\Finances\FinCategories\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;

class TransactiesRelationManager extends RelationManager
{
    protected static string $relationship = 'transacties';
    protected static ?string $title = 'Transacties';

    public function table(Table $table): Table
    {
        return $table

            ->defaultSort(
                fn($query) =>
                $query
                    ->orderByRaw('COALESCE(parent_id, id)')  // hoofdcategorie groepering
                    ->orderBy('parent_id')                    // subcategorieën na hoofdcategorie
                    ->orderBy('omschrijving')                 // alfabetisch binnen groep
            )
            ->columns([
                TextColumn::make('datum')
                    ->label('Datum')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('rekening.omschrijving')
                    ->label('Rekening')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('begunstigde.naam')
                    ->label('Begunstigde')
                    ->searchable(),

                TextColumn::make('omschrijving')
                    ->label('Omschrijving')
                    ->limit(40),

                TextColumn::make('bedrag')
                    ->label('Bedrag')
                    ->money('EUR')
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd()
                    ->searchable()
                    ->sortable(),

                IconColumn::make('verwerkt')
                    ->label('✓')
                    ->boolean(),
            ])
            ->defaultSort('datum', 'desc')
            ->filters([
                TernaryFilter::make('verwerkt')
                    ->label('Verwerkt')
            ]);
    }
}
