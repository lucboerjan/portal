<?php

namespace App\Filament\Resources\Investments\InvestmentPurchases\Pages;

use App\Filament\Resources\Investments\InvestmentPurchases\InvestmentPurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvestmentPurchase extends EditRecord
{
    protected static string $resource = InvestmentPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
