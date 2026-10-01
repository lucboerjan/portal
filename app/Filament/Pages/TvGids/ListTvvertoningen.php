<?php

namespace App\Filament\Pages\TvGids;

use App\Filament\Resources\TvGids\Tvvertoningen\TvvertoningResource;
use App\Filament\Resources\TvGids\Tvvertoningen\Schemas\TvvertoningSchema;
use App\Filament\Resources\TvGids\Tvvertoningen\Tables\TvvertoningTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class ListTvvertoningen extends ListRecords
{
    protected static string $resource = TvvertoningResource::class;

    public function form(Schema $schema): Schema
    {
        return TvvertoningSchema::make($schema);
    }

    /*     public function table(Table $table): Table
    {
        return TvvertoningTable::make($table);
    } */
    public function table(Table $table): Table
    {
        return TvvertoningTable::make(
            $table->modifyQueryUsing(
                fn(Builder $query) =>
                $query->leftJoin('vertoningen', 'imdbrating.id', '=', 'vertoningen.imdbrating_id')->addSelect('imdbrating.id as v_count', 'v_count.imdbrating_id', '=', 'vertoningen.imdbrating_id')
                    ->selectRaw('vertoningen.*, COUNT(v_count.id) as imdbrating_vertoningen_count')
                    ->groupBy('vertoningen.id')
            )
        );
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
