<?php

namespace App\Filament\Resources\Finances\FinAccounts\Pages;

use App\Filament\Resources\Finances\FinAccounts\FinAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\FinTransactie;

class EditFinAccount extends EditRecord
{
    protected static string $resource = FinAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $data['berekend_saldo'] = number_format($record->berekend_saldo, 2, '.', '');
        $data['verschil_saldo'] = number_format($record->verschil_saldo, 2, '.', '');
        return $data;
    }

    #[\Livewire\Attributes\On('refresh-page')]
    public function refreshPage(): void
    {
        $this->fillForm();
    }

    #[\Livewire\Attributes\On('recalculate-saldo')]
    public function recalculateSaldo(): void
    {
        $record = $this->getRecord();

        $this->data['berekend_saldo'] = number_format($record->berekend_saldo, 2, '.', '');
        $this->data['verschil_saldo'] = number_format($record->verschil_saldo, 2, '.', '');
    }
}
