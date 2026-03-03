<?php

namespace App\Filament\Resources\Finances\FinBegunstigdes\Pages;

use App\Filament\Resources\Finances\FinBegunstigdes\FinBegunstigdeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinBegunstigdes extends ListRecords
{
    protected static string $resource = FinBegunstigdeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
