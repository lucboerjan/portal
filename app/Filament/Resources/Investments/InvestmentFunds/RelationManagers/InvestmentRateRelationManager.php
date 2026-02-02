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

class InvestmentRateRelationManager extends RelationManager
{
    protected static string $relationship = 'InvestmentRate';

    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('datum')
                    ->required()
                    ->default(function () {
                        $last = \App\Models\InvestmentRate::where('fondsID', $this->ownerRecord->id)
                        ->orderBy('datum', 'desc')->first();

                        return $last
                            ? \Carbon\Carbon::parse($last->datum)->addDay()
                            : now();
                    })
                    ->displayFormat('d-m-Y')   // wat jij wil zien
                    ->native(false),           // mooie Filament datepicker

                TextInput::make('dagkoers')
                    ->required()
                    ->numeric()
                    ->label('Dagkoers'),
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
