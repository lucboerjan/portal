<?php

namespace App\Filament\Resources\Finances\FinTransactions;

use App\Filament\Resources\Finances\FinTransactions\Pages\CreateFinTransaction;
use App\Filament\Resources\Finances\FinTransactions\Pages\EditFinTransaction;
use App\Filament\Resources\Finances\FinTransactions\Pages\ListFinTransactions;
use App\Filament\Resources\Finances\FinTransactions\Schemas\FinTransactionForm;
use App\Filament\Resources\Finances\FinTransactions\Tables\FinTransactionsTable;
use App\Models\FinTransactie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinTransactionResource extends Resource
{
    protected static ?string $model = FinTransactie::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowsRightLeft;

    protected static ?string $navigationLabel = 'Transacties';
    protected static ?string $modelLabel = 'Transactie';
    protected static ?string $pluralModelLabel = 'Transacties';
    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return 'Financiën';
    }

    public static function form(Schema $schema): Schema
    {
        return FinTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinTransactionsTable::configure($table);
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
            'index' => ListFinTransactions::route('/'),
            'create' => CreateFinTransaction::route('/create'),
            'edit' => EditFinTransaction::route('/{record}/edit'),
        ];
    }

    // In EditFinTransactie.php page:
    protected function getRedirectUrl(): string
    {
        $transactie = $this->getRecord();

        return \App\Filament\Resources\Finances\FinAccounts\FinAccountResource::getUrl('edit', [
            'record' => $transactie->rekening_id
        ]);
    }
}
