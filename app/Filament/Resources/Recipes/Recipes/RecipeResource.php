<?php

namespace App\Filament\Resources\Recipes\Recipes;

use App\Filament\Resources\Recipes\Recipes\Pages\CreateRecipe;
use App\Filament\Resources\Recipes\Recipes\Pages\EditRecipe;
use App\Filament\Resources\Recipes\Recipes\Pages\ListRecipes;
use App\Filament\Resources\Recipes\Recipes\Pages\ViewRecipe;
use App\Filament\Resources\Recipes\Recipes\Schemas\RecipeForm;
use App\Filament\Resources\Recipes\Recipes\Schemas\RecipeWizardSteps;
use App\Filament\Resources\Recipes\Recipes\Tables\RecipesTable;
use App\Models\Recipes\Recipe;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BackedEnum;
use Filament\Support\Icons\Heroicon;


class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Recept';
    protected static ?string $pluralModelLabel = 'Recepten';

    public static function getNavigationGroup(): ?string
    {
        return 'Recepten';
    }



    public static function form(Schema $schema): Schema
    {
        return RecipeForm::configure($schema);
        //return RecipeWizardSteps::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecipesTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'view'   => ViewRecipe::route('/{record}'),
            'edit'   => EditRecipe::route('/{record}/edit'),
        ];
    }
}
