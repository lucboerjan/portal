<?php

namespace App\Filament\Resources\Finances\FinCategories\Pages;

use App\Filament\Resources\Finances\FinCategories\FinCategorieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinCategories extends ListRecords
{
    protected static string $resource = FinCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
