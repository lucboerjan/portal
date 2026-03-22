<?php

namespace App\Filament\Resources\GameScores;

use App\Filament\Resources\GameScores\GameResource\Pages;
use App\Filament\Resources\GameScores\GameResource\Schemas\GameSchema;
use App\Filament\Resources\GameScores\GameResource\Tables\GameTable;
use App\Models\Game;

use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

use Filament\Support\Icons\Heroicons;

class GameResource extends Resource
{
    protected static ?string $model            = Game::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;
    protected static ?string $navigationLabel  = 'Spellen';
    protected static ?string $modelLabel       = 'Spel';
    protected static ?string $pluralModelLabel = 'Spellen';

    protected static ?int    $navigationSort   = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Game Scores';
    }

    public static function form(Schema $schema): Schema
    {
        return GameSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListGames::route('/'),
            'create'    => Pages\CreateGame::route('/create'),
            'edit'      => Pages\EditGame::route('/{record}/edit'),
            'scorebord' => Pages\GameScoreboard::route('/{record}/scorebord'),
        ];
    }
}
