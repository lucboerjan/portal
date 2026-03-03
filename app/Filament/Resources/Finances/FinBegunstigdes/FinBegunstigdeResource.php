<?php

namespace App\Filament\Resources\Finances\FinBegunstigdes;

use App\Filament\Resources\Finances\FinBegunstigdes\Pages\CreateFinBegunstigde;
use App\Filament\Resources\Finances\FinBegunstigdes\Pages\EditFinBegunstigde;
use App\Filament\Resources\Finances\FinBegunstigdes\Pages\ListFinBegunstigdes;
use App\Filament\Resources\Finances\FinBegunstigdes\Schemas\FinBegunstigdeForm;
use App\Filament\Resources\Finances\FinBegunstigdes\Tables\FinBegunstigdesTable;
use App\Models\FinBegunstigde;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinBegunstigdeResource extends Resource
{
    protected static ?string $model = FinBegunstigde::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function form(Schema $schema): Schema
    {
        return FinBegunstigdeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinBegunstigdesTable::configure($table);
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
            'index' => ListFinBegunstigdes::route('/'),
            'create' => CreateFinBegunstigde::route('/create'),
            'edit' => EditFinBegunstigde::route('/{record}/edit'),
        ];
    }
}
