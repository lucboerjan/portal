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
            Select::make('tvzender_id')
                ->label('Televisiezender')
                ->options(
                    Tvzender::orderBy('naam')->pluck('naam', 'id')
                )
                ->required()
                ->searchable(),
            Select::make('imdbrating_id')
                ->label('Titel van de film')
                ->options(
                    Imdbrating::orderBy('titel')
                        ->whereNotNull('titel')  // ← filter null waarden uit
                        ->pluck('titel', 'id')
                )
                ->required()
                ->searchable(),
        ]);
    }
}
