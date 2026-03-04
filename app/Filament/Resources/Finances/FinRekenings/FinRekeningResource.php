<?php

namespace App\Filament\Resources\Finances\FinRekenings;

use App\Filament\Resources\Finances\FinRekenings\Schemas\FinRekeningsForm;
use App\Filament\Resources\Finances\FinRekenings\Tables\FinRekeningsTable;

use App\Filament\Resources\Finances\FinRekenings\Pages\CreateFinRekening;
use App\Filament\Resources\Finances\FinRekenings\Pages\EditFinRekening;
use App\Filament\Resources\Finances\FinRekenings\Pages\ListFinRekeningen;

use App\Models\FinRekening;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinRekeningResource extends Resource
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

    public static function form(Schema $schema): Schema
    {
        return FinRekeningsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinRekeningsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFinRekeningen::route('/'),
            'create' => CreateFinRekening::route('/create'),
            'edit'   => EditFinRekening::route('/{record}/edit'),
        ];
    }
}