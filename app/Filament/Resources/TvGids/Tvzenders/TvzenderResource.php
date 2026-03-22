<?php

namespace App\Filament\Resources\TvGids\Tvzenders;

use App\Filament\Resources\TvGids\Tvzenders\Pages\CreateTvzender;
use App\Filament\Resources\TvGids\Tvzenders\Pages\EditTvzender;
use App\Filament\Resources\TvGids\Tvzenders\Pages\ListTvzenders;
use App\Filament\Resources\TvGids\Tvzenders\Schemas\TvzenderForm;
use App\Filament\Resources\TvGids\Tvzenders\Tables\TvzendersTable;
use App\Models\Tvzender;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TvzenderResource extends Resource
{
    protected static ?string $model = Tvzender::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tv;



    protected static ?int    $navigationSort   = 8001;

    public static function getNavigationGroup(): ?string
    {
        return 'TV Gids';
    }

    public static function form(Schema $schema): Schema
    {
        return TvzenderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TvzendersTable::configure($table);
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
            'index' => ListTvzenders::route('/'),
            'create' => CreateTvzender::route('/create'),
            'edit' => EditTvzender::route('/{record}/edit'),
        ];
    }
}
