<?php

namespace App\Filament\Resources\Finances\FinTransacties\Pages;

use App\Filament\Resources\Finances\FinTransacties\FinTransactieResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinTransactie extends EditRecord
{
    protected static string $resource = FinTransactieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
