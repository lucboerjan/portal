<?php

namespace App\Filament\Resources\Utilities\UtilityTypes\Pages;

use App\Filament\Resources\Utilities\UtilityTypes\UtilityTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityTypes extends ListRecords
{
    protected static string $resource = UtilityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
