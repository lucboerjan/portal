<?php

namespace App\Filament\Resources\Recipes\Recipes\Pages;

use App\Filament\Resources\Recipes\Recipes\RecipeResource;
use App\Filament\Resources\Recipes\Recipes\Schemas\RecipeWizardSteps;

use Filament\Resources\Pages\CreateRecord;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            RecipeWizardSteps::getGeneralInfoStep(),
            RecipeWizardSteps::getIngredientsStep(),
            RecipeWizardSteps::getBereidingStep(),

        ];
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
