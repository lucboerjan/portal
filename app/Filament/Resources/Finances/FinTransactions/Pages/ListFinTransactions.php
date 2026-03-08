<?php

namespace App\Filament\Resources\Finances\FinTransactions\Pages;

use App\Filament\Resources\Finances\FinTransactions\FinTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListFinTransactions extends ListRecords
{
    protected static string $resource = FinTransactionResource::class;
    protected Width|string|null $maxContentWidth = 'full';
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
