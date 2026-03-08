<?php

namespace App\Filament\Resources\Finances\FinBeneficiaries\Pages;

use App\Filament\Resources\Finances\FinBeneficiaries\FinBeneficiaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListFinBeneficiaries extends ListRecords
{
    protected static string $resource = FinBeneficiaryResource::class;
    protected Width|string|null $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
