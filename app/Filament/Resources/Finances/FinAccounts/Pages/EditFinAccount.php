<?php

namespace App\Filament\Resources\Finances\FinAccounts\Pages;

use App\Filament\Resources\Finances\FinAccounts\FinAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinAccount extends EditRecord
{
    protected static string $resource = FinAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
