<?php

namespace App\Filament\Resources\Investments\InvestmentRates\Pages;

use App\Filament\Resources\Investments\InvestmentRates\InvestmentRateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvestmentRate extends EditRecord
{
    protected static string $resource = InvestmentRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
