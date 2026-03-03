<?php

namespace App\Filament\Resources\Finances\FinBegunstigdes\Pages;

use App\Filament\Resources\Finances\FinBegunstigdes\FinBegunstigdeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinBegunstigde extends EditRecord
{
    protected static string $resource = FinBegunstigdeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
