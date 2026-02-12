<?php

namespace App\Filament\Resources\Utilities\UtilityCorrections;

use App\Filament\Resources\Utilities\UtilityCorrections\Pages\CreateUtilityCorrection;
use App\Filament\Resources\Utilities\UtilityCorrections\Pages\EditUtilityCorrection;
use App\Filament\Resources\Utilities\UtilityCorrections\Pages\ListUtilityCorrections;
use App\Filament\Resources\Utilities\UtilityCorrections\Schemas\UtilityCorrectionForm;
use App\Filament\Resources\Utilities\UtilityCorrections\Tables\UtilityCorrectionsTable;
use App\Models\UtilityCorrection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UtilityCorrectionResource extends Resource
{
    protected static ?string $model = UtilityCorrection::class;

    //protected static string|UnitEnum|null $navigationGroup = 'Utilities';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Pencil;
    protected static ?int $navigationSort = 140;


    public static function form(Schema $schema): Schema
    {
        return UtilityCorrectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityCorrectionsTable::configure($table);
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
            'index' => ListUtilityCorrections::route('/'),
            'create' => CreateUtilityCorrection::route('/create'),
            'edit' => EditUtilityCorrection::route('/{record}/edit'),
        ];
    }


    public static function getNavigationGroup(): ?string
    {
        return 'Utilities';
    }


}
