<?php

namespace App\Filament\Resources\Recipes\Recipes\Pages;

use App\Filament\Resources\Recipes\Recipes\RecipeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecipe extends EditRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function afterCreate(): void
    {
        $this->syncIngredients();
    }

    // EditRecipe.php  
    protected function afterSave(): void
    {
        $this->syncIngredients();
    }

    // In beide — zet dit in een trait of herhaal:
    protected function syncIngredients(): void
    {
        $data = $this->data['ingredients_data'] ?? [];

        $sync = [];
        foreach (array_values($data) as $index => $row) {
            if (empty($row['ingredient_id'])) continue;

            $sync[$row['ingredient_id']] = [
                'quantity'   => $row['quantity'] ?? null,
                'unit'       => $row['unit'] ?? null,
                'notes'      => $row['notes'] ?? null,
                'sort_order' => $index,
            ];
        }

        $this->record->ingredients()->sync($sync);
    }

    protected function mutateFormDataBeforeSave(array $data): array  // EditRecipe
    {
        unset($data['ingredients_data']);
        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array  // CreateRecipe
    {
        unset($data['ingredients_data']);
        return $data;
    }
}
