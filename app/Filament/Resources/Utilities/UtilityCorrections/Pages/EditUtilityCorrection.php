<?php

namespace App\Filament\Resources\Utilities\UtilityCorrections\Pages;

use App\Filament\Resources\Utilities\UtilityCorrections\UtilityCorrectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUtilityCorrection extends EditRecord
{
    protected static string $resource = UtilityCorrectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
