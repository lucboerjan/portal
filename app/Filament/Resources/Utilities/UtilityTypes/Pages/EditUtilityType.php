<?php

namespace App\Filament\Resources\Utilities\UtilityTypes\Pages;

use App\Filament\Resources\Utilities\UtilityTypes\UtilityTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUtilityType extends EditRecord
{
    protected static string $resource = UtilityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
