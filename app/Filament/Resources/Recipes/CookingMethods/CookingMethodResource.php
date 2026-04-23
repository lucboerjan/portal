<?php

namespace App\Filament\Resources\Recipes\CookingMethods;

use App\Filament\Resources\Recipes\CookingMethods\Pages\CreateCookingMethod;
use App\Filament\Resources\Recipes\CookingMethods\Pages\EditCookingMethod;
use App\Filament\Resources\Recipes\CookingMethods\Pages\ListCookingMethods;
use App\Filament\Resources\Recipes\CookingMethods\Schemas\CookingMethodForm;
use App\Filament\Resources\Recipes\CookingMethods\Tables\CookingMethodsTable;
use App\Models\Recipes\CookingMethod;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BackedEnum;

class CookingMethodResource extends Resource
{
    protected static ?string $model = CookingMethod::class;
    protected static string|BackedEnum|null $navigationIcon = 'Heroicon::OutlinedFire';

    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Kookmethode';
    protected static ?string $pluralModelLabel = 'Kookmethodes';

    public static function getNavigationGroup(): ?string
    {
        return 'Recepten';
    }



    public static function form(Schema $schema): Schema
    {
        return CookingMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CookingMethodsTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCookingMethods::route('/'),
            'create' => CreateCookingMethod::route('/create'),
            'edit'   => EditCookingMethod::route('/{record}/edit'),
        ];
    }
}
