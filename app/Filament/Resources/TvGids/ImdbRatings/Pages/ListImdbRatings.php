<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Pages;

use App\Filament\Resources\TvGids\ImdbRatings\ImdbRatingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImdbRatings extends ListRecords
{
    protected static string $resource = ImdbRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
