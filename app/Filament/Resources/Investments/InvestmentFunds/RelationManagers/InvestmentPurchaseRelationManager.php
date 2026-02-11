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

class InvestmentPurchaseRelationManager extends RelationManager
{
    protected static string $relationship = 'InvestmentPurchase';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('datum')
                    ->required()
                    ->default(now())
                    ->displayFormat('d-m-Y')
                    ->native(false),

                TextInput::make('aantal')
                    ->required()
                    ->numeric()
                    ->minValue(0),


                TextInput::make('aankoopprijs')
                    ->required()
                    ->numeric()
                    ->minValue(0),

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
                TextColumn::make('aantal')
                    ->label('Aantal')
                    ->searchable(),

                TextColumn::make('aankoopprijs')
                    ->label('Aankoopprijs')
                    ->money('EUR', true)
                    ->searchable(),

                TextColumn::make('aankoopbedrag')
                    ->label('Aankoopbedrag')
                    ->money('EUR', true)
                    ->searchable(),

            ])
            ->defaultSort('datum', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                //AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                //DissociateAction::make(),
                //DeleteAction::make(),
            ])
            /* ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])*/
        ;
    }
}
