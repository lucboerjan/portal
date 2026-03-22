<?php

namespace App\Filament\Resources\Investments\InvestmentFunds\Pages;

use App\Filament\Resources\Investments\InvestmentFunds\InvestmentFundResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvestmentFund extends EditRecord
{
    protected static string $resource = InvestmentFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $data['berekend_saldo'] = number_format($record->rekening->berekend_saldo ?? 0, 2, '.', '');
        $data['rekening_naam'] = $record->rekening->omschrijving ?? '';
        return $data;
    }
}
