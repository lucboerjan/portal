<?php

namespace App\Filament\Resources\Investments\InvestmentRates\Pages;

use App\Filament\Resources\Investments\InvestmentRates\InvestmentRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvestmentRates extends ListRecords
{
    protected static string $resource = InvestmentRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
