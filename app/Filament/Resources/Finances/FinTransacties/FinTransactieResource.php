<?php

namespace App\Filament\Resources\Finances\FinTransacties;

use App\Filament\Resources\Finances\FinTransacties\Pages\CreateFinTransactie;
use App\Filament\Resources\Finances\FinTransacties\Pages\EditFinTransactie;
use App\Filament\Resources\Finances\FinTransacties\Pages\ListFinTransacties;
use App\Filament\Resources\Finances\FinTransacties\Schemas\FinTransactieForm;
use App\Filament\Resources\Finances\FinTransacties\Tables\FinTransactiesTable;
use App\Models\FinTransactie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinTransactieResource extends Resource
{
    protected static ?string $model = FinTransactie::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function form(Schema $schema): Schema
    {
        return FinTransactieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinTransactiesTable::configure($table);
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
            'index' => ListFinTransacties::route('/'),
            'create' => CreateFinTransactie::route('/create'),
            'edit' => EditFinTransactie::route('/{record}/edit'),
        ];
    }
}
