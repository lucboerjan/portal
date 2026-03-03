<?php

namespace App\Filament\Resources\Finances\FinTransacties\Pages;

use App\Filament\Resources\Finances\FinTransacties\FinTransactieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinTransacties extends ListRecords
{
    protected static string $resource = FinTransactieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
