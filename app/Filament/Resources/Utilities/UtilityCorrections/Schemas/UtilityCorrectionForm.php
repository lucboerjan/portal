<?php

namespace App\Filament\Resources\Utilities\UtilityCorrections\Schemas;

use Dom\Text;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\UtilityType;
use Filament\Forms\Components\DatePicker;

class UtilityCorrectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('utility_type_id')
                    ->label('Utility Type')
                    ->relationship('utilityType', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(
                        fn($state, callable $set) =>
                        $set('unit_display', UtilityType::find($state)?->unit ?? '')
                    ),
                DatePicker::make('correction_date')
                    ->label('Datum')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->default(now())
                    ->maxDate(now()),

                TextInput::make('old_meter_final_reading')
                    ->label('Old Meter Reading')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0),
                TextInput::make('new_meter_start_reading')
                    ->label('New Meter Reading')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0),
            ]);
    }
}
