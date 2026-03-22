<?php

namespace App\Filament\Resources\Utilities\UtilityReadings\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\UtilityType;
use App\Models\UtilityReading;
use Illuminate\Support\Facades\Log;

class UtilityReadingForm
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
                DatePicker::make('reading_date')
                    ->label('Datum')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->default(now()),
                //->maxDate(now()),

                TextInput::make('meter_stand')
                    ->label('Meterstand')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->suffix(fn($get) => UtilityType::find($get('utility_type_id'))?->unit ?? '')
                    ->hint(function ($get) {
                        $utilityTypeId = $get('utility_type_id');
                        if (!$utilityTypeId) {
                            return null;
                        }

                        $lastReading = UtilityReading::where('utility_type_id', $utilityTypeId)
                            ->orderBy('reading_date', 'desc')
                            ->first();

                        if (!$lastReading) {
                            return null;
                        }

                        return 'Laatste: ' . $lastReading->meter_stand;
                    })
                    ->hintColor('primary')
                    ->live(onBlur: true)


                    ->afterStateUpdated(function ($state, callable $set, ?UtilityReading  $record) {

                        if ($state <> $record->meter_stand) {
                            $set('reading_date', today()->format('Y-m-d'));
                        }
                    }),

                Textarea::make('notes')
                    ->label('Notities')
                    ->maxLength(65535)
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
