<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen\Pages;

use App\Filament\Resources\TvGids\Tvvertoningen\TvvertoningResource;
use App\Filament\Resources\TvGids\Tvvertoningen\Schemas\TvvertoningSchema;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditTvvertoning extends EditRecord
{
    protected static string $resource = TvvertoningResource::class;

    public function form(Schema $schema): Schema
    {
        return TvvertoningSchema::make($schema);
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}