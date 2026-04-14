<?php

namespace App\Filament\Resources\Recipes\Ingredients\Pages;

use App\Filament\Resources\Recipes\Ingredients\IngredientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIngredient extends CreateRecord
{
    protected static string $resource = IngredientResource::class;
}