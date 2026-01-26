<?php

namespace App\Filament\Resources\Utilities\UtilityCorrections\Pages;

use App\Filament\Resources\Utilities\UtilityCorrections\UtilityCorrectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityCorrections extends ListRecords
{
    protected static string $resource = UtilityCorrectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
