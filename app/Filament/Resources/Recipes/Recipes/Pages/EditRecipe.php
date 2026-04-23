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

    protected function afterSave(): void
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

    protected function mutateFormDataBeforeCreate(array $data): array  // CreateRecipe
    {
        unset($data['ingredients_data']);
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Laad de pivot-data om in de repeater te tonen
        $data['ingredients_data'] = $this->record->ingredients
            ->map(fn($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'quantity'      => $ingredient->pivot->quantity,
                'unit'          => $ingredient->pivot->unit,
                'notes'         => $ingredient->pivot->notes,
            ])
            ->values()
            ->toArray();

        return $data;
    }
}
