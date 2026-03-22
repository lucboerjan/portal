<?php

namespace App\Filament\Resources\TvGids\Tvzenders\Pages;

use App\Filament\Resources\TvGids\Tvzenders\TvzenderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTvzender extends EditRecord
{
    protected static string $resource = TvzenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
