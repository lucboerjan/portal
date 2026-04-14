<?php

namespace App\Filament\Resources\Recipes\CookingMethods\Pages;

use App\Filament\Resources\Recipes\CookingMethods\CookingMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCookingMethods extends ListRecords
{
    protected static string $resource = CookingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}