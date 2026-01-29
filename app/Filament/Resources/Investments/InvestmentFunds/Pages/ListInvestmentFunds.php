<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Pages;

use App\Filament\Resources\Investments\InvestmentFunds\InvestmentFundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvestmentFunds extends ListRecords
{
    protected static string $resource = InvestmentFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
