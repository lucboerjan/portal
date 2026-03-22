<?php

namespace App\Filament\Resources\TvGids\Tvzenders\Pages;

use App\Filament\Resources\TvGids\Tvzenders\TvzenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTvzenders extends ListRecords
{
    protected static string $resource = TvzenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
