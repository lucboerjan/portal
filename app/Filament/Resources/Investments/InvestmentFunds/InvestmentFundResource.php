<?php

namespace App\Filament\Resources\Investments\InvestmentFunds;

use App\Filament\Resources\Investments\InvestmentFunds\Pages\CreateInvestmentFund;
use App\Filament\Resources\Investments\InvestmentFunds\Pages\EditInvestmentFund;
use App\Filament\Resources\Investments\InvestmentFunds\Pages\ListInvestmentFunds;
use App\Filament\Resources\Investments\InvestmentFunds\RelationManagers\InvestmentPurchaseRelationManager;
use App\Filament\Resources\Investments\InvestmentFunds\Schemas\InvestmentFundForm;
use App\Filament\Resources\Investments\InvestmentFunds\Tables\InvestmentFundsTable;
use App\Models\InvestmentFund;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\Investments\InvestmentFunds\RelationManagers\InvestmentRateRelationManager;

class InvestmentFundResource extends Resource
{
    protected static ?string $model = InvestmentFund::class;
        protected static string|UnitEnum|null $navigationGroup = 'Investments';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return InvestmentFundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvestmentFundsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InvestmentRateRelationManager::class,
            InvestmentPurchaseRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvestmentFunds::route('/'),
            'create' => CreateInvestmentFund::route('/create'),
            'edit' => EditInvestmentFund::route('/{record}/edit'),
        ];
    }
}
