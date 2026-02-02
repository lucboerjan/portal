<?php

namespace App\Filament\Resources\Investments\InvestmentPurchases;

use App\Filament\Resources\Investments\InvestmentPurchases\Pages\CreateInvestmentPurchase;
use App\Filament\Resources\Investments\InvestmentPurchases\Pages\EditInvestmentPurchase;
use App\Filament\Resources\Investments\InvestmentPurchases\Pages\ListInvestmentPurchases;
use App\Filament\Resources\Investments\InvestmentPurchases\Schemas\InvestmentPurchaseForm;
use App\Filament\Resources\Investments\InvestmentPurchases\Tables\InvestmentPurchasesTable;
use App\Models\InvestmentPurchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InvestmentPurchaseResource extends Resource
{
    protected static ?string $model = InvestmentPurchase::class;
        protected static string|UnitEnum|null $navigationGroup = 'Investments';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    public static function shouldRegisterNavigation(): bool
{
    return false;
}


    public static function form(Schema $schema): Schema
    {
        return InvestmentPurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvestmentPurchasesTable::configure($table);
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
            'index' => ListInvestmentPurchases::route('/'),
            'create' => CreateInvestmentPurchase::route('/create'),
            'edit' => EditInvestmentPurchase::route('/{record}/edit'),
        ];
    }
}
