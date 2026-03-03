<?php

namespace App\Filament\Resources\Finances\FinCategories\Pages;

use App\Filament\Resources\Finances\FinCategories\FinCategorieResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinCategorie extends EditRecord
{
    protected static string $resource = FinCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
