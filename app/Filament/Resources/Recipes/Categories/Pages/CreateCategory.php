<?php

namespace App\Filament\Resources\Recipes\Categories\Pages;

use App\Filament\Resources\Recipes\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}