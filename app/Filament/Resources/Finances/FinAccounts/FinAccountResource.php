<?php

namespace App\Filament\Resources\Finances\FinAccounts;

use App\Filament\Resources\Finances\FinAccounts\Pages\CreateFinAccount;
use App\Filament\Resources\Finances\FinAccounts\Pages\EditFinAccount;
use App\Filament\Resources\Finances\FinAccounts\Pages\ListFinAccounts;
use App\Filament\Resources\Finances\FinAccounts\Schemas\FinAccountForm;
use App\Filament\Resources\Finances\FinAccounts\Tables\FinAccountsTable;
use App\Models\FinRekening;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinAccountResource extends Resource
{
    protected static ?string $model = FinRekening::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;
    protected static ?string $navigationLabel = 'Rekeningen';
    protected static ?string $modelLabel = 'Rekening';
    protected static ?string $pluralModelLabel = 'Rekeningen';
    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function table(Table $table): Table
    {
        return FinAccountsTable::configure($table);
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
            'index' => ListFinAccounts::route('/'),
            'create' => CreateFinAccount::route('/create'),
            'edit' => EditFinAccount::route('/{record}/edit'),
        ];
    }
}
