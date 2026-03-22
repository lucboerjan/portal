<?php

namespace App\Filament\Resources\GameScores;

use App\Filament\Resources\GameScores\GameTypeResource\Pages;
use App\Filament\Resources\GameScores\GameTypeResource\Schemas\GameTypeSchema;
use App\Filament\Resources\GameScores\GameTypeResource\Tables\GameTypeTable;
use App\Models\GameType;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class GameTypeResource extends Resource
{
    protected static ?string $model             = GameType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PuzzlePiece;

    protected static ?string $navigationLabel   = 'Speltypes';
    protected static ?string $modelLabel        = 'Speltype';
    protected static ?string $pluralModelLabel  = 'Speltypes';
    
    protected static ?int    $navigationSort    = 2;

        public static function getNavigationGroup(): ?string
    {
        return 'Game Scores';
    }


        public static function form(Schema $schema): Schema
    {
        return GameTypeSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameTypeTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGameTypes::route('/'),
            'create' => Pages\CreateGameType::route('/create'),
            'edit'   => Pages\EditGameType::route('/{record}/edit'),
        ];
    }
}
