<?php

namespace App\Filament\Resources\GameScores\GameResource\Pages;

use App\Filament\Resources\GameScores\GameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGames extends ListRecords
{
    protected static string $resource = GameResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Spel toevoegen')];
    }
}
