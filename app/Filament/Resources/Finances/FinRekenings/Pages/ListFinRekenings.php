<?php

namespace App\Filament\Resources\Finances\FinRekenings\Pages;

use App\Filament\Resources\Finances\FinRekenings\FinRekeningResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinRekenings extends ListRecords
{
    protected static string $resource = FinRekeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
