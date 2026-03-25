<?php

namespace App\Filament\Resources\Utilities\UtilityReadings\Pages;

use App\Filament\Resources\Utilities\UtilityReadings\UtilityReadingResource;
use App\Models\UtilityReading;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityReadings extends ListRecords
{
    protected static string $resource = UtilityReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        $latest = UtilityReading::whereIn('utility_type_id', [4, 6, 7, 8, 9])
            ->latest('updated_at')
            ->first();

        $tijd = $latest ? $latest->updated_at->locale('nl')->isoFormat('dddd H:mm') : null;

        return 'Meterstanden Utilities' . ($tijd ? " (Laatste uitlezing {$tijd})" : '');
    }
}