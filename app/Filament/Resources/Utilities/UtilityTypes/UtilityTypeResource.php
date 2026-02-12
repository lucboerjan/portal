<?php

namespace App\Filament\Resources\Utilities\UtilityTypes;

use App\Filament\Resources\Utilities\UtilityTypes\Pages\CreateUtilityType;
use App\Filament\Resources\Utilities\UtilityTypes\Pages\EditUtilityType;
use App\Filament\Resources\Utilities\UtilityTypes\Pages\ListUtilityTypes;
use App\Filament\Resources\Utilities\UtilityTypes\Schemas\UtilityTypeForm;
use App\Filament\Resources\Utilities\UtilityTypes\Tables\UtilityTypesTable;
use App\Models\UtilityType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UtilityTypeResource extends Resource
{
    protected static ?string $model = UtilityType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;
    protected static ?int $navigationSort = 30;

    
    public static function form(Schema $schema): Schema
    {
        return UtilityTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUtilityTypes::route('/'),
            'create' => CreateUtilityType::route('/create'),
            'edit' => EditUtilityType::route('/{record}/edit'),
        ];
    }

        public static function getNavigationGroup(): ?string
    {
        return 'Utilities';
    }
}