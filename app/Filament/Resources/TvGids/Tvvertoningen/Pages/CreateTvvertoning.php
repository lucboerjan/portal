<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen\Pages;

use App\Filament\Resources\TvGids\Tvvertoningen\TvvertoningResource;
use App\Filament\Resources\TvGids\Tvvertoningen\Schemas\TvvertoningSchema;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\CreateRecord;

class CreateTvvertoning extends CreateRecord
{
    protected static string $resource = TvvertoningResource::class;

    public function form(Schema $schema): Schema
    {
        return TvvertoningSchema::make($schema);
    }
}