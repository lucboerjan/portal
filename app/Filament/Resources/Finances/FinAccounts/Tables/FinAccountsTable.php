<?php

namespace App\Filament\Resources\Finances\FinAccounts\Tables;

use App\Enums\RekeningType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;


class FinAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 75, 100, 200, 'all'])
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('omschrijving')
                    ->label('Rekening')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('referentie')
                    ->label('Referentie')
                    ->searchable()
                    ->copyable()        // handig voor rekeningnummers
                    ->copyMessage('Gekopieerd!')
                    ->fontFamily('mono'),

                TextColumn::make('rekening_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        RekeningType::Zichtrekening         => 'info',
                        RekeningType::Spaarrekening         => 'success',
                        RekeningType::Maaltijdcheques       => 'warning',
                        RekeningType::Beleggingsrekening    => 'primary',
                        RekeningType::Pensioenspaarrekening => 'danger',
                    }),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd()
                    ->extraAttributes(['class' => 'privacy-sensitive'])
                    ->summarize([
                        Sum::make()
                            ->label('Totaal')
                            ->money('EUR')
                            ->extraAttributes(['class' => 'privacy-sensitive']),

                    ]),

                IconColumn::make('actief')
                    ->label('Actief')
                    ->boolean()
                    ,
            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('rekening_type')
                    ->label('Type')
                    ->options(
                        collect(RekeningType::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
                    ),
            ])
            ->recordActions([
                ActionGroup::make([


                    EditAction::make(),
                    DeleteAction::make(),
                ]),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
