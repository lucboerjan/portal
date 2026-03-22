<?php

namespace App\Filament\Resources\GameScores\GameResource\Pages;

use App\Filament\Resources\GameScores\GameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGame extends CreateRecord
{
    protected static string $resource = GameResource::class;

    protected function getRedirectUrl(): string
    {
        return GameResource::getUrl('scorebord', ['record' => $this->getRecord()]);
    }
}
