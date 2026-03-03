<?php

namespace App\Filament\Resources\Finances\FinCategories;

use App\Filament\Resources\Finances\FinCategories\Schemas\FinCategorieForm;
use App\Filament\Resources\Finances\FinCategories\Tables\FinCategorieTable;

use App\Filament\Resources\Finances\FinCategories\Pages\CreateFinCategorie;
use App\Filament\Resources\Finances\FinCategories\Pages\EditFinCategorie;
use App\Filament\Resources\Finances\FinCategories\Pages\ListFinCategories;

use App\Models\FinCategorie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinCategorieResource extends Resource
{
    protected static ?string $model = FinCategorie::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;
    protected static ?string $navigationLabel = 'Categorieën';
    protected static ?string $modelLabel = 'Categorie';
    protected static ?string $pluralModelLabel = 'Categorieën';
    protected static ?int $navigationSort = 200;

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function form(Schema $schema): Schema
    {
        return FinCategorieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinCategorieTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFinCategories::route('/'),
            'create' => CreateFinCategorie::route('/create'),
            'edit'   => EditFinCategorie::route('/{record}/edit'),
        ];
    }
}