<?php

namespace App\Filament\Resources\Finances\FinCategories\Tables;

use App\Enums\CategorieRichting;
use App\Models\FinCategorie;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinCategorieTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 20, 50, 'all'])
            ->defaultPaginationPageOption(50)
            ->modifyQueryUsing(
                fn($query) =>
                $query
                    ->withSum('transacties', 'bedrag')
                    ->geordend()
            )
            ->columns([
                TextColumn::make('parent.omschrijving')
                    ->label('Hoofdcategorie')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('omschrijving')
                    ->label('Categorie')
                    ->searchable()
                    /*                     ->sortable(query: function ($query, string $direction) {
                        return $query
                            ->orderByRaw('COALESCE(parent_id, id) ' . $direction)
                            ->orderBy('parent_id', $direction)
                            ->orderBy('omschrijving', $direction);
                    }) */
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->isHoofdcategorie() ? $state : '↳ ' . $state
                    ),

                TextColumn::make('richting')
                    ->label('Richting')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        CategorieRichting::Inkomst => 'success',
                        CategorieRichting::Uitgave => 'danger',
                    }),

                IconColumn::make('exclude')
                    ->label('Uitgesloten')
                    ->boolean(),

                IconColumn::make('actief')
                    ->label('Actief')
                    ->boolean(),

                TextColumn::make('transacties_count')
                    ->label('# Transacties')
                    ->counts('transacties')
                    ->sortable(),

                TextColumn::make('transacties_sum_bedrag')
                    ->label('Totaal bedrag')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')),
            ])
            ->filters([
                SelectFilter::make('richting')
                    ->options(
                        collect(CategorieRichting::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                    ),
                SelectFilter::make('parent_id')
                    ->label('Hoofdcategorie')
                    ->options(
                        FinCategorie::whereNull('parent_id')->pluck('omschrijving', 'id')
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
