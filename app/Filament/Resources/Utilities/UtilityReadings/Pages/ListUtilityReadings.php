<?php

namespace App\Filament\Resources\Utilities\UtilityReadings\Pages;

use App\Filament\Resources\Utilities\UtilityReadings\UtilityReadingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityReadings extends ListRecords
{
    protected static string $resource = UtilityReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
