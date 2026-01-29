<?php

namespace App\Filament\Resources\Investments\InvestmentRates;

use App\Filament\Resources\Investments\InvestmentRates\Pages\CreateInvestmentRate;
use App\Filament\Resources\Investments\InvestmentRates\Pages\EditInvestmentRate;
use App\Filament\Resources\Investments\InvestmentRates\Pages\ListInvestmentRates;
use App\Filament\Resources\Investments\InvestmentRates\Schemas\InvestmentRateForm;
use App\Filament\Resources\Investments\InvestmentRates\Tables\InvestmentRatesTable;
use App\Models\InvestmentRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InvestmentRateResource extends Resource
{
    protected static ?string $model = InvestmentRate::class;
            protected static string|UnitEnum|null $navigationGroup = 'Investments';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    public static function form(Schema $schema): Schema
    {
        return InvestmentRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvestmentRatesTable::configure($table);
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
            'index' => ListInvestmentRates::route('/'),
            'create' => CreateInvestmentRate::route('/create'),
            'edit' => EditInvestmentRate::route('/{record}/edit'),
        ];
    }
}
