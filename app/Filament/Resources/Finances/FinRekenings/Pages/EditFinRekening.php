<?php

namespace App\Filament\Resources\Finances\FinRekenings\Pages;

use App\Filament\Resources\Finances\FinRekenings\FinRekeningResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinRekening extends EditRecord
{
    protected static string $resource = FinRekeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
