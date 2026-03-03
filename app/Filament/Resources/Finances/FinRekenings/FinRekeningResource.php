<?php

namespace App\Filament\Resources\Finances\FinRekenings;

use App\Filament\Resources\Finances\FinRekenings\Pages\CreateFinRekening;
use App\Filament\Resources\Finances\FinRekenings\Pages\EditFinRekening;
use App\Filament\Resources\Finances\FinRekenings\Pages\ListFinRekenings;
use App\Filament\Resources\Finances\FinRekenings\Schemas\FinRekeningForm;
use App\Filament\Resources\Finances\FinRekenings\Tables\FinRekeningsTable;
use App\Models\FinRekening;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinRekeningResource extends Resource
{
    protected static ?string $model = FinRekening::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
        public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function form(Schema $schema): Schema
    {
        return FinRekeningForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinRekeningsTable::configure($table);
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
            'index' => ListFinRekenings::route('/'),
            'create' => CreateFinRekening::route('/create'),
            'edit' => EditFinRekening::route('/{record}/edit'),
        ];
    }
}
