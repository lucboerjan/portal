<?php

namespace App\Filament\Resources\Finances\FinTransactions\Pages;

use App\Filament\Resources\Finances\FinTransactions\FinTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinTransaction extends EditRecord
{
    protected static string $resource = FinTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
