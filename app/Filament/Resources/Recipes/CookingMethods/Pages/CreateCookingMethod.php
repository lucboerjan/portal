<?php

namespace App\Filament\Resources\Recipes\CookingMethods\Pages;

use App\Filament\Resources\Recipes\CookingMethods\CookingMethodResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCookingMethod extends CreateRecord
{
    protected static string $resource = CookingMethodResource::class;
}