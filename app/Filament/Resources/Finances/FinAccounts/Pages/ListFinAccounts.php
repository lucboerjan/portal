<?php

namespace App\Filament\Resources\Finances\FinAccounts\Pages;

use App\Filament\Resources\Finances\FinAccounts\FinAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinAccounts extends ListRecords
{
    protected static string $resource = FinAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
