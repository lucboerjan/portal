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
}
