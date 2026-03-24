<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen;

use App\Filament\Resources\TvGids\Tvvertoningen\Pages;
use App\Models\Vertoning;
use Filament\Resources\Resource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
class TvvertoningResource extends Resource
{
    protected static ?string $model = Vertoning::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;
    protected static ?string $navigationLabel = 'TV Vertoningen';
    protected static ?string $modelLabel = 'TV Vertoning';
    protected static ?string $pluralModelLabel = 'TV Vertoningen';
    protected static ?int $navigationSort = 1;

        public static function getNavigationGroup(): ?string
    {
        return 'TV Gids';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTvvertoningen::route('/'),
            'create' => Pages\CreateTvvertoning::route('/create'),
            'edit'   => Pages\EditTvvertoning::route('/{record}/edit'),
        ];
    }
}
