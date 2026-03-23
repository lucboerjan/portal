<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen\Pages;

use App\Filament\Resources\TvGids\Tvvertoningen\TvvertoningResource;
use App\Filament\Resources\TvGids\Tvvertoningen\Schemas\TvvertoningSchema;
use App\Filament\Resources\TvGids\Tvvertoningen\Tables\TvvertoningTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class ListTvvertoningen extends ListRecords
{
    protected static string $resource = TvvertoningResource::class;

    public function form(Schema $schema): Schema
    {
        return TvvertoningSchema::make($schema);
    }

    public function table(Table $table): Table
    {
        return TvvertoningTable::make($table);
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}