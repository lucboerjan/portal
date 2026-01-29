<?php

namespace App\Filament\Resources\Investments\InvestmentPurchases\Pages;

use App\Filament\Resources\Investments\InvestmentPurchases\InvestmentPurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvestmentPurchases extends ListRecords
{
    protected static string $resource = InvestmentPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
