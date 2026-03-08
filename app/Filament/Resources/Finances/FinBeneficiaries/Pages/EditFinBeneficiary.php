<?php

namespace App\Filament\Resources\Finances\FinBeneficiaries\Pages;

use App\Filament\Resources\Finances\FinBeneficiaries\FinBeneficiaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinBeneficiary extends EditRecord
{
    protected static string $resource = FinBeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
