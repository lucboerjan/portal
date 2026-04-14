<?php

namespace App\Filament\Resources\Recipes\Categories;

use App\Filament\Resources\Recipes\Categories\Pages\CreateCategory;
use App\Filament\Resources\Recipes\Categories\Pages\EditCategory;
use App\Filament\Resources\Recipes\Categories\Pages\ListCategories;
use App\Filament\Resources\Recipes\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Recipes\Categories\Tables\CategoriesTable;
use App\Models\Recipes\Category;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BackedEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';



    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Categorie';
    protected static ?string $pluralModelLabel = 'Categorieën';

    public static function getNavigationGroup(): ?string
    {
        return 'Recepten';
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit'   => EditCategory::route('/{record}/edit'),
        ];
    }
}
