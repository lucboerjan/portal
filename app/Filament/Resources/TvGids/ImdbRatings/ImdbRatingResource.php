<?php

namespace App\Filament\Resources\TvGids\ImdbRatings;

use App\Filament\Resources\TvGids\ImdbRatings\Pages\CreateImdbRating;
use App\Filament\Resources\TvGids\ImdbRatings\Pages\EditImdbRating;
use App\Filament\Resources\TvGids\ImdbRatings\Pages\ListImdbRatings;
use App\Filament\Resources\TvGids\ImdbRatings\Schemas\ImdbRatingForm;
use App\Filament\Resources\TvGids\ImdbRatings\Tables\ImdbRatingsTable;
use App\Models\Imdbrating;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ImdbRatingResource extends Resource
{
    protected static ?string $model = Imdbrating::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;
    protected static ?int $navigationSort = 3;

        public static function getNavigationGroup(): ?string
        
    {
        return 'TV Gids';
    }

    public static function form(Schema $schema): Schema
    {
        return ImdbRatingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImdbRatingsTable::configure($table);
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
            'index' => ListImdbRatings::route('/'),
            'create' => CreateImdbRating::route('/create'),
            'edit' => EditImdbRating::route('/{record}/edit'),
        ];
    }
}
