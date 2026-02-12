<?php

namespace App\Filament\Resources\Utilities\UtilityReadings;

use App\Filament\Resources\Utilities\UtilityReadings\Pages\CreateUtilityReading;
use App\Filament\Resources\Utilities\UtilityReadings\Pages\EditUtilityReading;
use App\Filament\Resources\Utilities\UtilityReadings\Pages\ListUtilityReadings;
use App\Filament\Resources\Utilities\UtilityReadings\Schemas\UtilityReadingForm;
use App\Filament\Resources\Utilities\UtilityReadings\Tables\UtilityReadingsTable;
use App\Models\UtilityReading;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UtilityReadingResource extends Resource
{
    protected static ?string $model = UtilityReading::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    protected static ?int $navigationSort = 120;

    public static function form(Schema $schema): Schema
    {
        return UtilityReadingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityReadingsTable::configure($table);
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
            'index' => ListUtilityReadings::route('/'),
            'create' => CreateUtilityReading::route('/create'),
            'edit' => EditUtilityReading::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Utilities';
    }
}
