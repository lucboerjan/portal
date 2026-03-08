<?php

namespace App\Filament\Resources\Finances\FinBeneficiaries;

use App\Filament\Resources\Finances\FinBeneficiaries\Pages\CreateFinBeneficiary;
use App\Filament\Resources\Finances\FinBeneficiaries\Pages\EditFinBeneficiary;
use App\Filament\Resources\Finances\FinBeneficiaries\Pages\ListFinBeneficiaries;
use App\Filament\Resources\Finances\FinBeneficiaries\Schemas\FinBeneficiaryForm;
use App\Filament\Resources\Finances\FinBeneficiaries\Tables\FinBeneficiariesTable;
use App\Models\FinBegunstigde;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinBeneficiaryResource extends Resource
{
    protected static ?string $model = FinBegunstigde::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Begunstigden';
    protected static ?string $modelLabel = 'Begunstigde';
    protected static ?string $pluralModelLabel = 'Begunstigden';
    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }
    
    public static function form(Schema $schema): Schema
    {
        return FinBeneficiaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinBeneficiariesTable::configure($table);
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
            'index' => ListFinBeneficiaries::route('/'),
            'create' => CreateFinBeneficiary::route('/create'),
            'edit' => EditFinBeneficiary::route('/{record}/edit'),
        ];
    }
}
