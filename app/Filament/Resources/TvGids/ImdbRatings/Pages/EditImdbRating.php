<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Pages;

use App\Filament\Resources\TvGids\ImdbRatings\ImdbRatingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImdbRating extends EditRecord
{
    protected static string $resource = ImdbRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
