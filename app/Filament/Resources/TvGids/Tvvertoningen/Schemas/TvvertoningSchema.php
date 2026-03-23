<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen\Schemas;

use App\Models\Imdbrating;
use App\Models\Tvzender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TvvertoningSchema
{
    public static function make(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('datum')
                ->label('Datum vertoning')
                ->required()
                ->displayFormat('d/m/Y')
                ->format('Y-m-d'),
            Select::make('tvzenderID')
                ->label('Televisiezender')
                ->options(
                    Tvzender::orderBy('volgnummer')->pluck('naam', 'id')
                )
                ->required()
                ->searchable(),
            Select::make('imdbratingID')
                ->label('Titel van de film')
                ->options(
                    Imdbrating::orderBy('titel')->pluck('titel', 'id')
                )
                ->required()
                ->searchable(),
        ]);
    }
}