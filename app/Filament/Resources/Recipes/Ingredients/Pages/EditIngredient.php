<?php

namespace App\Filament\Resources\Recipes\Ingredients\Pages;

use App\Filament\Resources\Recipes\Ingredients\IngredientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIngredient extends EditRecord
{
    protected static string $resource = IngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}