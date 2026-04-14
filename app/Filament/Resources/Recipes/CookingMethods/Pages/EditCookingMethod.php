<?php

namespace App\Filament\Resources\Recipes\CookingMethods\Pages;

use App\Filament\Resources\Recipes\CookingMethods\CookingMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCookingMethod extends EditRecord
{
    protected static string $resource = CookingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}